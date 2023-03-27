<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg">

                <form action="/admin/users/store" method="post">
                    {{ csrf_field() }}
                    <div class="flex justify-center items-center">
                        <div class="w-full md:w-1/2 flex flex-col items-center ">
                            <!-- text login -->
                            <h1 class="text-center text-2xl font-semibold text-gray-700 mb-6">Crear un usuario</h1>
                            <!-- name input -->
                            <div class="w-3/4 mb-6 border-1 border-gray-800">
                                <input type="name" name="name" id="name"
                                    class="w-full py-4 px-8 bg-slate-200 placeholder:font-semibold rounded hover:ring-1 outline-blue-500"
                                    placeholder="Nombre">
                                @if ($errors->has('name'))
                                    <span class="text-red-500 text-xs italic">{{ $errors->first('name') }}</span>
                                @endif
                            </div>
                            <!-- lastname input -->
                            <div class="w-3/4 mb-6">
                                <input type="lastname" name="lastname" id="lastname"
                                    class="w-full py-4 px-8 bg-slate-200 placeholder:font-semibold rounded hover:ring-1 outline-blue-500"
                                    placeholder="Apellido">
                                @if ($errors->has('lastname'))
                                    <span class="text-red-500 text-xs italic">{{ $errors->first('lastname') }}</span>
                                @endif
                            </div>
                            <!-- email input -->
                            <div class="w-3/4 mb-6">
                                <input type="email" name="email" id="email"
                                    class="w-full py-4 px-8 bg-slate-200 placeholder:font-semibold rounded hover:ring-1 outline-blue-500"
                                    placeholder="Email">
                                @if ($errors->has('email'))
                                    <span class="text-red-500 text-xs italic">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                            <!-- password input -->
                            <div class="w-3/4 mb-6">
                                <input type="password" name="password" id="password"
                                    class="w-full py-4 px-8 bg-slate-200 placeholder:font-semibold rounded hover:ring-1 outline-blue-500 "
                                    placeholder="Contraseña">
                                @if ($errors->has('password'))
                                    <span class="text-red-500 text-xs italic">{{ $errors->first('password') }}</span>
                                @endif
                            </div>
                            <!-- password_confirmation input -->
                            <div class="w-3/4 mb-6">
                                <input type="password" name="password_confirmation"
                                    id="password_confirmation"
                                    class="w-full py-4 px-8 bg-slate-200 placeholder:font-semibold rounded hover:ring-1 outline-blue-500 "
                                    placeholder="Confirmar contraseña">
                                @if ($errors->has('password_confirmation'))
                                    <span class="text-red-500 text-xs italic">{{ $errors->first('password_confirmation') }}</span>
                                @endif
                            </div>

                            <!-- button -->
                            <div class="w-3/4 mt-4">
                                <button type="submit"
                                    class="py-4 mb-8 bg-blue-400 w-full rounded text-blue-50 font-bold hover:bg-blue-700">
                                    Guardar</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
