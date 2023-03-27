<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CommunityFormWizard extends Component
{
    public $steps = [
        'Paso uno',
        'Paso dos',
    ];

    public $logo;
    public $name;
    public $users;

    public $currentStep = 1;
    public $totalSteps;
    public $uuid;

    public $storeValuesInSession = false;
    protected $queryString = ['uuid'];

    protected $rules = [
        'name' => 'required',
        'logo' => 'required',
        'users' => 'required',
    ];

    public function render()
    {
        return view('components.community-form-wizard');
    }

    public function mount()
    {
        $this->getValuesFromSession();
        $this->totalSteps = count($this->steps);
    }

    public function next()
    {
        if ($this->currentStep < $this->totalSteps) {
            if (isset($this->rules[$this->currentStep - 1])) {
                $this->validate($this->rules[$this->currentStep - 1]);
            }
            $this->currentStep++;
            $this->storeValuesInSession();
            $this->emit('moveNext');
        }
    }

    public function submit()
    {
        $this->validateMultipleSteps(1, $this->totalSteps);  //Validate all steps on final submit
        $this->emit('formSubmit');
    }

    public function previous()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->storeValuesInSession();
            $this->emit('movePrevious');
        }
    }

    public function switchStep($step)
    {
        if ($step > $this->currentStep) {
            $this->validateMultipleSteps($this->currentStep, $step);  //Validate steps from current till the step user wants to swtich to
        }
        $this->storeValuesInSession();
        $this->currentStep = $step;
        $this->emit('switchStep');
    }

    protected function validateMultipleSteps($fromStep, $toStep)
    {
        for ($i = $fromStep - 1; $i < $toStep - 1; $i++) {
            if (isset($this->rules[$this->currentStep - 1])) {
                $data = $this->getDataForValidation($this->rules[$i]);
                $validated = Validator::make($data, $this->rules[$i]);
                if ($validated->fails()) {
                    $this->currentStep = $i + 1;
                    $this->storeValuesInSession();
                    $this->validate($this->rules[$i]);
                    return;
                }
            }
        }
    }

    protected function storeValuesInSession()
    {
        if ($this->storeValuesInSession) {
            session([$this->uuid => get_object_vars($this)]);
        }
    }

    protected function getValuesFromSession()
    {
        if ($this->storeValuesInSession) {
            if (!isset($this->uuid) || !Session::has($this->uuid)) {
                $this->uuid = (string) Str::uuid();
                $this->storeValuesInSession();
            } else {
                //Set values in object from session
                $values = session($this->uuid);
                foreach ($values as $key => $value) {
                    $this->$key = $value;
                }
            }
        }
    }
}
