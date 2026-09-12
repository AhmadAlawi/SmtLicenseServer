@extends('dashboard.layout')
@section('title', 'Blog Posts')
@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <h1 style="margin:0">Blog Posts</h1>
        <a href="{{ route('dashboard.blog-posts.create') }}" class="btn">New post</a>
    </div>
    <table>
        <thead><tr><th>Title</th><th>Slug</th><th>Published</th><th>Updated</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach ($posts as $post)
            <tr>
                <td>{{ $post->title }}</td>
                <td>{{ $post->slug }}</td>
                <td>{{ $post->is_published ? 'Yes' : 'No' }}</td>
                <td>{{ $post->updated_at->format('Y-m-d') }}</td>
                <td>
                    <a href="{{ route('dashboard.blog-posts.edit', $post) }}">Edit</a>
                    <form class="inline" method="POST" action="{{ route('dashboard.blog-posts.toggle-published', $post) }}" style="display:inline">
                        @csrf
                        <button type="submit">{{ $post->is_published ? 'Unpublish' : 'Publish' }}</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
