<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Validation;
use App\Services\ValidationService;
use Illuminate\Support\Facades\Auth;

class ValidationIndicator extends Component
{
    public $pendingValidations = [];
    public $showValidationModal = false;
    public $selectedValidation = null;
    public $approvalComments = '';
    public $rejectionReason = '';

    protected $listeners = [
        'refreshValidations' => 'loadPendingValidations',
        'validationRequested' => 'loadPendingValidations'
    ];

    public function mount()
    {
        $this->loadPendingValidations();
    }

    public function loadPendingValidations()
    {
        $user = Auth::user();
        $validationService = app(ValidationService::class);
        
        $this->pendingValidations = $validationService->getPendingValidationsForUser($user);
    }

    public function showValidationDetails($validationId)
    {
        $this->selectedValidation = Validation::with(['requestedBy', 'validatedBy'])->find($validationId);
        $this->showValidationModal = true;
    }

    public function closeValidationModal()
    {
        $this->showValidationModal = false;
        $this->selectedValidation = null;
        $this->approvalComments = '';
        $this->rejectionReason = '';
    }

    public function approveValidation()
    {
        if (!$this->selectedValidation) {
            return;
        }

        $user = Auth::user();
        $validationService = app(ValidationService::class);

        try {
            $validationService->approveValidation($this->selectedValidation, $user, $this->approvalComments);
            
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Validation approuvée avec succès'
            ]);

            $this->closeValidationModal();
            $this->loadPendingValidations();
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
                'message' => 'Veuillez fournir une raison de rejet'
            ]);
            return;
        }

        $user = Auth::user();
        $validationService = app(ValidationService::class);

        try {
            $validationService->rejectValidation($this->selectedValidation, $user, $this->rejectionReason);
            
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Validation rejetée avec succès'
            ]);

            $this->closeValidationModal();
            $this->loadPendingValidations();
        } catch (\Exception $e) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.validation-indicator');
    }
} 