@php($post = $post ?? null)
@if ($errors->any())
    <div class="flash" style="background:#fbe1e1">{{ $errors->first() }}</div>
@endif
<form method="POST" action="{{ $post ? route('dashboard.blog-posts.update', $post) : route('dashboard.blog-posts.store') }}" style="max-width:720px">
    @csrf
    @if ($post) @method('PUT') @endif

    <label>Title</label>
    <input type="text" name="title" value="{{ old('title', $post?->title) }}" required>

    <label>Slug</label>
    <input type="text" name="slug" value="{{ old('slug', $post?->slug) }}" placeholder="running-multiple-branches" required>
    <div class="hint">URL: /blog/{slug}</div>

    <label>Excerpt (shown on the blog index)</label>
    <textarea name="excerpt" rows="2" required>{{ old('excerpt', $post?->excerpt) }}</textarea>

    <label>Meta description (SEO — falls back to excerpt if blank)</label>
    <input type="text" name="meta_description" value="{{ old('meta_description', $post?->meta_description) }}">

    <label>Body</label>
    <textarea name="body" rows="16" required>{{ old('body', $post?->body) }}</textarea>

    <label><input type="checkbox" name="is_published" value="1" {{ old('is_published', $post?->is_published ?? true) ? 'checked' : '' }}> Published (visible on the public blog)</label>

    <button type="submit" class="btn" style="margin-top:16px">{{ $post ? 'Save changes' : 'Create post' }}</button>
</form>
