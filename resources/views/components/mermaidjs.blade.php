@once
    @push('footer_js')
        <script type="module">
            import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
            mermaid.initialize({ startOnLoad: true });
        </script>
    @endpush
@endonce
<div class="row">
 <pre class="mermaid">{!! $code !!}</pre>
     <button class="btn btn-primary mt-3" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $id }}"
            aria-expanded="false"
            aria-controls="{{ $id }}">Show MermaidJS code
    </button>
    <div class="collapse multi-collapse" id="{{ $id }}">
<pre><code class="language-promql">
{{ $code }}
</code></pre>
    </div>
</div>
