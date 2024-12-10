<x-app>
    <div class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <form action="{{ route('store.appointment') }}" method="POST" class="space-y-6">
            @csrf
            <livewire:appointment-form />
            <!-- Submit Button -->
            <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border rounded-md shadow-sm text-sm font-medium text-black bg-green-600 ">Submit</button>

        </form>

        <!-- Notifications -->
        @if (session()->has('error'))
            <div class="mt-4 text-sm text-red-600">{{ session('error') }}</div>
        @endif
    
        @if (session()->has('success'))
            <div class="mt-4 text-sm text-green-600">{{ session('success') }}</div>
        @endif
    </div>
</x-app>
