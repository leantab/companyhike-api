@php
    if(isset($i['required_if'])){
        $explode = explode(':', $i['required_if']);
        $field = $explode[0];
        $values = explode(',', $explode[1]);

        if(isset($data[$field]) && in_array($data[$field], $values)){
            $display = true;
        }else{
            $display = false;
        }
    }else{
        $display = true;
    }
@endphp

@if($display )
    @if($i['type'] == "string")
        <div class="form-group">
            <div class="row">
                <div class="col-3">
                    <label for="">{{ $name }}</label>
                    <input wire:model.lazy="data.{{$name}}" id="{{$name}}" type="text" class="form-control" placeholder="">
                </div>
                <div class="col-9">
                    <div class="card text-monospace">
                        <small><pre class="mb-0">{{ json_encode($i, JSON_PRETTY_PRINT) }}</pre></small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($i['type'] == "integer")
        <div class="form-group">
            <div class="row">
                <div class="col-3">
                    <label for="">{{ $name }}</label>
                    <input wire:model.lazy="data.{{$name}}" id="{{$name}}" type="number" class="form-control" placeholder="">
                </div>
                <div class="col-9">
                    <div class="card text-monospace">
                        <small><pre class="mb-0">{{ json_encode($i, JSON_PRETTY_PRINT) }}</pre></small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($i['type'] == "options")

        <div class="form-group">
            <div class="row">
                    <div class="col-3">
                        <label for="exampleFormControlSelect1">{{ $name }}</label>
                        <select wire:model.lazy="data.{{$name}}" id="{{$name}}" class="form-control" @if($name == 'type') wire:change="typeChanged()"@endif>
                            <option value="">-</option>
                            @if(isset($i['options']))
                                @foreach($i['options'] as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-9">
                        <div class="card text-monospace">
                            <small><pre class="mb-0">{{ json_encode($i, JSON_PRETTY_PRINT) }}</pre></small>
                        </div>
                    </div>
                </div>
            </div>

    @endif
    <hr />
@endif
