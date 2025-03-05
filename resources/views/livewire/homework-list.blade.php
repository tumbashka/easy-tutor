<x-card.card>
    <x-card.header :title="'Домашнее задание'"/>
    <x-card.body>
        @if(!$homeworks->count())
            <p class="text-center h5">Список пуст</p>
        @else
            @foreach($homeworks as $homework)
                <livewire:homework-item :homework="$homework" :key="$homework->id"/>
            @endforeach
        @endif
    </x-card.body>
    <x-card.footer>
        {{ $homeworks->links() }}
        <x-link-button :href="route('students.homeworks.create', $studentId)">Добавить</x-link-button>
    </x-card.footer>
</x-card.card>

