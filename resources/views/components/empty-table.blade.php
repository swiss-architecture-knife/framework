@props([
    'cols' => 1
])
<tr>
    <td colspan="{{ $cols }}">
        <x-swark::alert>{{ __('swark::g.table.empty') }}</x-swark::alert>
    </td>
</tr>
