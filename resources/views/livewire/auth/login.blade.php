<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-8">
            <!-- Ganti src dengan logo Anda -->
            <img src="{{ asset('logo-sakip (1).png') }}" alt="Logo" class="w-20 h-auto mx-auto mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Selamat Datang</h2>
            <p class="text-sm text-gray-500">Silakan login untuk masuk ke aplikasi</p>
        </div>

        <form wire:submit="login">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" wire:model="email" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none transition" placeholder="admin@example.com">
                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" wire:model="password" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none transition" placeholder="********">
                @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition duration-200 shadow-md flex justify-center items-center">
                <span wire:loading.remove>Login Masuk</span>
                <span wire:loading class="animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full"></span>
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} Dinas Kesehatan. All rights reserved.</p>
        </div>
    </div>
</div>