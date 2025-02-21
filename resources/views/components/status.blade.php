@props([
    'status' => 'open'
])
<small class="text-body-secondary">
<span
    class="badge bg-{{ ($status == 'done') ? 'success' : 'primary' }}">{{ $status }}</span>
</small>
