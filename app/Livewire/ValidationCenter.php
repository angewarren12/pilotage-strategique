<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Validation;
use App\Services\ValidationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ValidationCenter extends Component
{
    public $showValidations = false;
    public $validations = [];
    public $selectedValidation = null;
    public $showValidationDetails = false;
    public $approvalComments = '';
    public $rejectionReason = '';
    public $stats = [];

    protected $listeners = [
        'refreshValidations' => 'loadValidations',
        'validationRequested' => 'handleNewValidation'
    ];

    public function mount()
    {
        $this->loadValidations();
        $this->loadStats();
    }

    public function loadValidations()
    {
        $user = Auth::user();
        $validationService = app(ValidationService::class);
        
        $this->validations = $validationService->getPendingValidationsForUser($user);
    }

    public function loadStats()
    {
        $validationService = app(ValidationService::class);
        $this->stats = $validationService->getValidationStats();
    }

    public function toggleValidations()
    {
        $this->showValidations = !$this->showValidations;
        
        if ($this->showValidations) {
            $this->loadValidations();
        }
    }

    public function showValidationDetails($validationId)
    {
        $this->selectedValidation = Validation::with(['requestedBy', 'validatedBy'])
            ->find($validationId);
        
        if ($this->selectedValidation) {
            $this->showValidationDetails = true;
        }
    }

    public function closeValidationDetails()
    {
        $this->showValidationDetails = false;
        $this->selectedValidation = null;
        $this->approvalComments = '';
        $this->rejectionReason = '';
    }

    public function approveValidation()
    {
        if (!$this->selectedValidation) {
            return;
        }

        try {
            $user = Auth::user();
            $validationService = app(ValidationService::class);
            
            $success = $validationService->approveValidation(
                $this->selectedValidation,
                $user,
                $this->approvalComments
            );

            if ($success) {
                $this->dispatch('showToast', [
                    'type' => 'success',
                    'message' => 'Validation approuvée avec succès.'
                ]);
                
                $this->closeValidationDetails();
                $this->loadValidations();
                $this->loadStats();
            }
        } catch (\Exception $e) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function rejectValidation()
    {
        if (!$this->selectedValidation || empty($this->rejectionReason)) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Veuillez fournir une raison de rejet.'
            ]);
            return;
        }

        try {
            $user = Auth::user();
            $validationService = app(ValidationService::class);
            
            $success = $validationService->rejectValidation(
                $this->selectedValidation,
                $user,
                $this->rejectionReason
            );

            if ($success) {
                $this->dispatch('showToast', [
                    'type' => 'success',
                    'message' => 'Validation rejetée.'
                ]);
                
                $this->closeValidationDetails();
                $this->loadValidations();
                $this->loadStats();
            }
        } catch (\Exception $e) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function handleNewValidation($data)
    {
        $this->loadValidations();
        $this->loadStats();
        
        $this->dispatch('showToast', [
            'type' => 'info',
            'message' => 'Nouvelle demande de validation reçue.'
        ]);
    }

    public function getValidationIcon($status)
    {
        return match($status) {
            'approved' => 'fas fa-check-circle text-success',
            'rejected' => 'fas fa-times-circle text-danger',
            'pending' => 'fas fa-clock text-primary',
            default => 'fas fa-question-circle text-secondary'
        };
    }

    public function getValidationColor($status)
    {
        return match($status) {
            'approved' => 'success',
            'rejected' => 'danger',
            'pending' => 'primary',
            default => 'secondary'
        };
    }

    public function canValidate($validation)
    {
        if (!$validation) {
            return false;
        }

        $user = Auth::user();
        return $validation->canBeValidatedBy($user);
    }

    public function render()
    {
        return view('livewire.validation-center');
    }
}
