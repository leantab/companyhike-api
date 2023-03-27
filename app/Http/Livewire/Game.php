<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Leantab\Sherpa\Facades\Sherpa;
use Leantab\Sherpa\Models\Game as ModelsGame;

class Game extends Component
{
    public $matchId;

    public $userId = 1; // admin
    public $stage = 0;

    public $schema;
    public $variables;
    public $ranking;
    public $displayMatchParameters = true;
    public $data = [];

    public $search = '';

    public $match_name;
    public $match_type;
    public $match_stages;
    public $match_current_stage;
    public $match_completed;
    public $match_parameters;
    public $match_results;
    public $match_is_goverment;
    public $match_has_goverment_decisions;
    public $match_has_all_ceo_decisions;
    public $company_results;
    public $company_name;
    public $company_bankrupt;
    public $goverment_decisions;
    public $ceo_dismissed;
    public $ceos_results = [];
    public $match_ceos = [];

    public function render()
    {
        $this->generate();
        return view('livewire.game');
    }

    public function generate()
    {
        $this->matchId = ModelsGame::latest()->first()->id;
        $match = Sherpa::getGame($this->matchId);
        $this->match_name = $match->name;
        $this->match_type = $match->type;
        $this->match_stages = $match->stages;
        $this->match_current_stage = $match->current_stage;
        $this->match_completed = $match->isCompleted();
        $this->match_is_goverment = $match->isGoverment($this->userId);
        $this->match_has_goverment_decisions = $match->hasGovermentDecisions();
        $this->match_has_all_ceo_decisions = $match->hasAllCeoDecisions();
        $this->match_parameters = $match->game_parameters;
        if($match->current_stage > $this->stage || $this->match_completed){
            $this->match_results = $match->results['stage_'.$this->stage];
        }

        $ceo = $match->ceos()->where('user_id', $this->userId)->first();
        $ceos = $match->ceos()->get();

        if($ceo){
            $this->company_name = $ceo->pivot->company_name;
            $this->company_bankrupt = $ceo->pivot->bankrupt;
            $this->ceo_dismissed = $ceo->pivot->dismissed;
        }else{
            if($this->stage > 0 && ($this->stage < $match->current_stage || $this->match_completed)){
                $this->goverment_decisions = $match->goverment_parameters['stage_' . $this->stage];
            }else{
                $this->goverment_decisions = null;
            }
        }
        if($ceo && ($match->current_stage > $this->stage || $this->match_completed)){

            $this->company_results = $ceo->pivot->results['stage_'.$this->stage];
        }

        $ceos_results = [];
        foreach ($ceos as $ceo) {
            $ceos_results[$ceo->id] = $ceo->pivot->results['stage_'.$this->stage];
        }
        $this->ceos_results = $ceos_results;

        $this->match_ceos = [];
        foreach($match->ceos as $ceo){
            $this->match_ceos[$ceo->id] = [
                'company_name' => $ceo->pivot->company_name,
                'user_id' => $ceo->pivot->user_id,
                'bankrupt' => $ceo->pivot->bankrupt,
                'dismissed' => $ceo->pivot->dismissed,
            ];
        }

        $this->ranking = Sherpa::getGameRanking($this->matchId, $this->stage);
        $this->schema = Sherpa::getSchema($match->version);
        $this->variables = Sherpa::getvariables($match->version);
    }

    public function changeStage($currentStage)
    {
        $this->stage = $currentStage;
        $this->generate();
    }

    public function changeUser($currentUser)
    {
        $this->userId = $currentUser;
        if ($this->stage >= $this->match_current_stage) {
            if($this->userId == 1){
                $this->data = Sherpa::getGovermentParameters($this->matchId, $this->stage);
            }else{
                $this->data = Sherpa::getCeoParameters($this->matchId, $this->stage, $this->userId);
            }
        }
        $this->generate();
    }

    public function submit(){

        if($this->userId == 1){
            $update = Sherpa::setGovermentParameters($this->matchId, $this->data);
            if ($update->status) {
                session()->flash('message', 'Variables de gobierno actualizadas con éxito.');
                $this->emit('scrollTop');

            } else {
                dd($update->errors);
            }
        }else{
            $update = Sherpa::setCeoParameters($this->matchId, $this->data, $this->userId);
            if ($update->status) {
                session()->flash('message', 'Variables de CEO actualizadas con éxito.');
                $this->emit('scrollTop');
            } else {
                dd($update->errors);
            }
        }
    }

    public function reprocess(){
        $res = Sherpa::reprocessGame($this->matchId, $this->stage);
        if($res){
            session()->flash('message', 'Turno reprocesado');
        }else{
            session()->flash('message', 'ERROR');
        }
        $this->emit('scrollTop');
    }

    public function process(){
        $res = Sherpa::processGame($this->matchId);
        if ($res) {
            session()->flash('message', 'Turno procesado');
        } else {
            session()->flash('message', 'ERROR');
        }
    }

    public function forceProcess(){
        $res = Sherpa::forceProcessGame($this->matchId);
        if ($res) {
            session()->flash('message', 'Turno procesado');
        } else {
            session()->flash('message', 'ERROR');
        }
    }

    public function processRandom(){
        $res = Sherpa::processNextStepTest($this->matchId);
        if ($res) {
            session()->flash('message', 'Turno procesado');
        } else {
            session()->flash('message', 'ERROR');
        }
    }

    public function clearSearch(){
        $this->search = '';
    }

    public function changeDisplayMatchParameters(){
        $this->displayMatchParameters = !$this->displayMatchParameters;
    }
}
