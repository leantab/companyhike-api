<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Comunidades') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg h-screen">
                <form action="/admin/communities/store" method="post">
                    {{ csrf_field() }}
                    <div class="w-3/4 m-auto min-h-full flex flex-col justify-around">
                        <h2 >Crear una comunidad</h2>

                        <label for="name">Nombre de la Comunidad</label>
                        <input type="text" name="name" id="name" class="w-full border rounded p-2" placeholder="Nombre de la Comunidad">
                        @error('name')
                            <span class="text-red-500 text-xs italic">{{ $message }}</span>
                        @enderror

                        <label for="logo" class="mt-3">Logo</label>
                        <input type="file" name="logo" id="logo" class="w-full border rounded p-2" placeholder="Logo">

                        @livewire('select2-multiple')

                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded font-medium w-full mt-3">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
