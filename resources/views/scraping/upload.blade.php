<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Upload') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-5">
                    <form action="{{ route('image.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4 space-y-1">
                            <x-label for="image">Upload Image</x-label>
                            <input type="file" name="image" id="image"
                                class="px-2 py-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        </div>
                        <div class="flex items-center justify-between">
                            <x-button>Enviar</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
