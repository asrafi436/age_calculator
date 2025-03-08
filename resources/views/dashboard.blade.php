<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <div class="py-12">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Age Calculator</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ url('/dashboard') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="birthdate" class="form-label">Enter your birthdate:</label>
                                    <input type="date" id="birthdate" name="birthdate" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Calculate Age</button>
                            </form>

                            @isset($ageMessage)
                                <h1 class="mt-3 text-center">{{ $ageMessage }}</h1>
                            @endisset
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
