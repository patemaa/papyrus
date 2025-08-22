{{-- resources/views/filament/pages/analytics.blade.php --}}
<x-filament-panels::page>
    <div class="bg-red-500 text-white p-4 mb-4 rounded">
        🔴 Kırmızı görünüyorsa Tailwind çalışıyor!
    </div>

    <div class="bg-blue-500 text-white p-4 mb-4 rounded">
        🔵 Mavi görünüyorsa tamam!
    </div>


    <div class="bg-red-500 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-500 rounded-lg">
                    <x-heroicon-o-document-text class="h-6 w-6 text-white" />
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Toplam Notlar</p>
                    <p class="text-2xl font-bold">{{ $totalNotes }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-500 rounded-lg">
                    <x-heroicon-o-check-circle class="h-6 w-6 text-white" />
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Yayınlanan</p>
                    <p class="text-2xl font-bold">{{ $publishedNotes }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-500 rounded-lg">
                    <x-heroicon-o-pencil-square class="h-6 w-6 text-white" />
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Taslaklar</p>
                    <p class="text-2xl font-bold">{{ $draftNotes }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-500 rounded-lg">
                    <x-heroicon-o-folder class="h-6 w-6 text-white" />
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Kategoriler</p>
                    <p class="text-2xl font-bold">{{ $categoriesCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">En Popüler Kategoriler</h3>
            <div class="space-y-3">
                @foreach($topCategories as $category)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $category->color }}"></div>
                            <span>{{ $category->name }}</span>
                        </div>
                        <span class="font-semibold">{{ $category->notes_count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Son Aktivite</h3>
            <div class="space-y-3">
                @foreach($recentActivity as $note)
                    <div class="flex items-center justify-between text-sm">
                        <div>
                            <p class="font-medium">{{ \Str::limit($note->title, 30) }}</p>
                            <p class="text-gray-600">{{ $note->user->name }} • {{ $note->category?->name }}</p>
                        </div>
                        <span class="text-gray-500">{{ $note->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-filament-panels::page>
