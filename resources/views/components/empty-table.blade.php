@props([
    'cols' => 1
])
<tr>
    <td colspan="{{ $cols }}">
        <x-swark::alert>{{ __('swark::g.table.empty') }}
            {{ $slot }}
        </x-swark::alert>
    </td>
</tr>
