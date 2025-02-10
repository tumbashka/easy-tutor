@props([
    'name' => '',
    'first_tab_name' => '',
    'second_tab_name' => '',
])
<x-card.card>
    <x-card.header :title="$name"/>
    <x-card.body class="p-1">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane"
                        type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">
                    {{ $first_tab_name }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link " id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane"
                        type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">
                    {{ $second_tab_name }}
                </button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active m-2" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab"
                 tabindex="0">
                {{ $first_tab_data_slot }}
            </div>
            <div class="tab-pane fade m-2" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab"
                 tabindex="0">
                {{ $second_tab_data_slot }}
            </div>
        </div>
    </x-card.body>
    <x-card.footer/>
</x-card.card>
