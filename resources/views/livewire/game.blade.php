<div class="row mt-2">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <div class="col-1">
        <div class="m-2 h4" style="font-family: 'B612 Mono', monospace;"> Partida {{ $match_name }} </div>
        <div class="m-2 h6 text-muted"> {{ $match_type }}</div>
        <div class="list-group">

            @for($i=0; $i<=$match_stages; $i++)
                <button wire:click="changeStage({{ $i }});" class="@if($match_current_stage < $i) disabled @endif list-group-item list-group-item-action @if($stage == $i) active @endif nav-link w-120px">
                    Turno {{ $i }}
                </button>
            @endfor
        </div>
    </div>
    <div class="col-11 pl-5">

        <div class="tab-content">
            <div class="p-3">

                <div class="row my-2">
                    <div class="col-2 h6">
                        <span class="border border-warning p-2"> Stage: {{ $stage }}</span>
                    </div>
                    <div class="col-2 h6">
                        <span class="border border-warning p-2"> UserId: {{ $userId }}</span>
                    </div>
                </div>

                @if (session()->has('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                @endif

                @if($match_current_stage > $stage || $this->match_completed)
                    <div class="row d-flex justify-content-between aling-items-center mb-3">
                        <form class="form-inline my-2 my-lg-0 col">
                            <input wire:model="search" class="form-control" type="text" placeholder="Search" aria-label="Search">
                            <button wire:click="clearSearch()" class="btn btn-purple my-2 my-sm-0" type="submit">
                                <i class="fas fa-trash-alt m-1"></i>
                            </button>
                        </form>
                        @if(false === $this->match_completed)
                            <button wire:click="reprocess()" class="btn btn-purple float-right">Reprocesar turno</button>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header text-center">
                                    <span style="font-family: 'B612 Mono', monospace;"> Valores iniciales de la partida</span><a wire:click.prevent="changeDisplayMatchParameters" href="#"><i class="fas fa-eye m-1"></i></a>
                                </div>
                                @if($displayMatchParameters)
                                <div class="card-body">
                                    @php
                                    ksort($match_parameters);

                                    if($this->search != ''){
                                            $chunks = array_chunk(array_filter($match_parameters, function($key){
                                                if(strpos($key, $this->search) !== false){
                                                    return true;
                                                }else{
                                                    return false;
                                                }
                                            }, ARRAY_FILTER_USE_KEY), 4, true);
                                        }else{
                                            $chunks = array_chunk($match_parameters, 6, true);
                                        }

                                @endphp
                                @foreach($chunks as $chunk)
                                <div class="row">
                                    @include('livewire.label-display', ['chunk' => $chunk, 'col' => 2])
                                </div>
                                @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header text-center">
                                    <span style="font-family: 'B612 Mono', monospace;"> Resultados globales del turno</span>
                                </div>
                                <div class="card-body">
                                    @php
                                    ksort($match_results);

                                    if($this->search != ''){
                                            $chunks = array_chunk(array_filter($match_results, function($key){
                                                if(strpos($key, $this->search) !== false){
                                                    return true;
                                                }else{
                                                    return false;
                                                }
                                            }, ARRAY_FILTER_USE_KEY), 4, true);
                                        }else{
                                            $chunks = array_chunk($match_results, 6, true);
                                        }

                                @endphp
                                @foreach($chunks as $chunk)
                                <div class="row">
                                    @include('livewire.label-display', ['chunk' => $chunk, 'col' => 2])
                                </div>
                                @endforeach
                                </div>
                            </div>
                        </div>

                        @if($match_is_goverment)
                            @if($stage > 0)
                                <div class="col-4">
                                    <div class="card">
                                    <div class="card-header text-center">
                                        <span style="font-family: 'B612 Mono', monospace;">Decisiones de gobierno </span>
                                    </div>
                                    <div class="card-body">

                                        @php
                                            ksort($goverment_decisions);

                                            if($this->search != ''){
                                                    $chunks = array_chunk(array_filter($goverment_decisions, function($key){
                                                        if(strpos($key, $this->search) !== false){
                                                            return true;
                                                        }else{
                                                            return false;
                                                        }
                                                    }, ARRAY_FILTER_USE_KEY), 4, true);
                                                }else{
                                                    $chunks = array_chunk($goverment_decisions, 2, true);
                                                }

                                        @endphp

                                        @foreach($chunks as $chunk)
                                        <div class="row">
                                            @include('livewire.label-display', ['chunk' => $chunk, 'col' => 6])
                                        </div>
                                        @endforeach

                                    </div>
                                    </div>
                                </div>
                                @endif
                            <div class="col-2">
                                <div class="card">
                                  <div class="card-header text-center">
                                      <span style="font-family: 'B612 Mono', monospace;">Ranking </span>
                                  </div>
                                  <div class="card-body">
                                      @foreach($this->ranking as $ranking)
                                        {{ $ranking['position'] }} - {{ $ranking['company_name'] }} <br />
                                      @endforeach
                                  </div>
                                </div>
                              </div>
                        @endif

                        @if(!$match_is_goverment)

                        <div class="row">
                        @foreach ($ceos_results as $company => $company_results)
                        <div class="col-3">
                                    <div class="card">
                                        <div class="card-header text-center">
                                           <span style="font-family: 'B612 Mono', monospace;">Resultados {{ $match_ceos[$company]['company_name'] }}</span>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                ksort($company_results);

                                                $chunks = array_chunk($company_results, 2, true);

                                            @endphp
                                            @foreach($chunks as $chunk)
                                            <div class="row">
                                                @include('livewire.label-display', ['chunk' => $chunk, 'col' => 6])
                                            </div>
                                            @endforeach

                                        </div>
                                    </div>
                        </div>
                        @endforeach
                        </div>
                        @endif
                    </div>


                @else
                    <div class="row d-flex justify-content-between aling-items-center mb-3">
                        <form class="form-inline my-2 my-lg-0 col">
                            <input wire:model="search" class="form-control" type="text" placeholder="Search" aria-label="Search">
                            <button wire:click="clearSearch()" class="btn btn-purple my-2 my-sm-0" type="submit">
                                <i class="fas fa-trash-alt m-1"></i>
                            </button>
                        </form>
                        @if($userId == 1)
                            @if($match_has_goverment_decisions)
                                @if(!$this->match_completed)
                                    @if($match_has_all_ceo_decisions)
                                        <button wire:click="process()" class="btn btn-purple float-right">Procesar turno</button>
                                        @else
                                        <button wire:click="forceProcess()" class="btn btn-purple float-right">Forzar procesar turno</button>
                                    @endif
                                @endif
                            @else
                                <button disabled class="btn btn-purple float-right" title="Completar decisiones de gobierno">Procesar turno</button>

                            @endif
                        @endif
                    </div>

                    <form wire:submit.prevent="submit">
                        @if($userId == 1)
                            <div class="row">
                                <div class="col-8">
                                    <div class="card">
                                        <div class="card-header text-center">
                                            <strong>Gobierno </strong>
                                        </div>
                                         <div class="card-body">
                                            @forelse(Sherpa::getGovermentVariables($this->matchId) as $name => $i)

                                                @include('livewire.form-builder', ['name' => $name, 'i' => $i])

                                                @if ($loop->last)
                                                    <button type="submit" class="btn btn-orange float-right">Guardar decisiones del gobierno</button>
                                                @endif

                                            @empty
                                                <div>El gobierno no debe tomar decisiones</div>

                                            @endforelse
                                         </div>

                                    </div>
                                </div>
                        @else
                                <div class="col-8">
                                    <div class="card">
                                        <div class="card-header text-center">
                                            <strong>Empresa: {{ $company_name }} </strong>
                                        </div>
                                        <div class="card-body">
                                            @if($company_bankrupt)
                                            <span>La empresa esta en quiebra</span>
                                            @elseif($ceo_dismissed)
                                            <span>El CEO fue despedido</span>
                                            @else
                                                @foreach(Sherpa::getCeoVariables($this->matchId, $userId) as $name => $i)

                                                    @include('livewire.form-builder', ['name' => $name, 'i' => $i])

                                                @endforeach
                                                <button type="submit" class="btn btn-orange float-right" >Guardar decisiones del CEO</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                @endif


            </div>
        </div>
    </div>
</div>
