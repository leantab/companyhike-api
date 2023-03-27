<div>
    <div wire:ignore>
        <label for="taskSelect" class="form-label">Seleccionar Usuarios</label>
        <br>
        <select class="form-select" id="taskSelect" multiple="multiple" class="w-full rounded-md" >
            @foreach($users as $user)
                <option id="{{$user->id}}">{{ $user->full_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="my-3">
        Usuarios seleccionados
        @forelse($selectedUsers as $user)
            {{ $user->full_name }} ,
        @empty
            None
        @endforelse
    </div>

    <script>
        $(document).ready(function() {
            $('#taskSelect').select2();

            $('#taskSelect').on('change', function (e) {
                @this.set('selectedUsers', $(this).val());
            });
        });
    </script>
</div>
