@extends('layouts.admin')
@section('content')
    <style>
        .post-card { border: 1px solid #e7eaec; border-radius: 8px; background: #fff; margin-bottom: 25px; overflow: hidden; }
        .post-thumb-wrap { background: #f3f3f4; height: 220px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .post-thumb-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .post-thumb-placeholder { color: #888; font-size: 12px; }
        .insta-modal-dialog { width: 95%; max-width: 1100px; }
        .insta-modal-body { padding: 0; }
        .insta-layout { display: flex; min-height: 600px; }
        .insta-left { width: 62%; background: #111; display: flex; align-items: center; justify-content: center; }
        .insta-left img { max-width: 100%; max-height: 85vh; object-fit: contain; }
        .insta-right { width: 38%; background: #fff; display: flex; flex-direction: column; border-left: 1px solid #efefef; }
        .insta-right-head, .insta-right-foot { padding: 15px; border-bottom: 1px solid #efefef; }
        .insta-right-foot { border-top: 1px solid #efefef; border-bottom: 0; margin-top: auto; }
        .insta-comments { padding: 15px; max-height: 430px; overflow-y: auto; }
        .insta-comment-item { margin-bottom: 12px; }
        @media (max-width: 991px) {
            .insta-layout { display: block; }
            .insta-left, .insta-right { width: 100%; }
            .insta-comments { max-height: 260px; }
        }
    </style>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Posts</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Posts</strong></li>
            </ol>
        </div>
    </div>
    @php
        $hasFilters = filled(request('q')) || filled(request('user_id')) || filled(request('created_from')) || filled(request('created_to'));
    @endphp
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row m-b-sm">
            <div class="col-sm-6">
                <p class="text-muted m-b-none">Review and moderate user posts.</p>
            </div>
            <div class="col-sm-6 text-right">
                <button type="button" class="btn btn-primary" id="openCreatePostModal" data-toggle="modal" data-target="#createPostModal">
                    <i class="fa fa-plus"></i> Create Post
                </button>
                <button type="button" class="btn btn-default {{ $hasFilters ? '' : 'collapsed' }}" data-toggle="collapse" data-target="#postFilters" id="togglePostFilters">
                    <i class="fa fa-filter"></i> {{ $hasFilters ? 'Hide filters' : 'Show filters' }}
                </button>
            </div>
        </div>
        <div id="postFilters" class="collapse {{ $hasFilters ? 'in' : '' }} m-b-md">
            <div class="ibox">
                <div class="ibox-content">
                    <form method="GET" action="{{ route('admin.posts.index') }}" class="row">
                        <div class="col-md-4"><div class="form-group"><label>Search</label><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Title or description"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>User</label><select name="user_id" class="form-control js-select2"><option value="">All</option>@foreach ($users as $u)<option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>@endforeach</select></div></div>
                        <div class="col-md-2"><div class="form-group"><label>From</label><input class="form-control js-flatpickr-date" name="created_from" value="{{ request('created_from') }}" placeholder="YYYY-MM-DD"></div></div>
                        <div class="col-md-2"><div class="form-group"><label>To</label><input class="form-control js-flatpickr-date" name="created_to" value="{{ request('created_to') }}" placeholder="YYYY-MM-DD"></div></div>
                        <div class="col-md-2"><div class="form-group"><label>Per page</label><select class="form-control" name="per_page">@foreach ([10,15,25,50,100] as $pp)<option value="{{ $pp }}" {{ (int) request('per_page', 15) === $pp ? 'selected' : '' }}>{{ $pp }}</option>@endforeach</select></div></div>
                        <div class="col-md-10 text-right">
                            <button class="btn btn-primary"><i class="fa fa-search"></i> Apply</button>
                            <a href="{{ route('admin.posts.index') }}" class="btn btn-white">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            @forelse ($posts as $post)
                <div class="col-md-2">
                    <div class="post-card">
                        <div class="post-thumb-wrap">
                            @if (!empty($post->thumbnail))
                                <img src="{{ filter_var($post->thumbnail, FILTER_VALIDATE_URL) ? $post->thumbnail : asset('storage/' . ltrim($post->thumbnail, '/')) }}" alt="Post image">
                            @else
                                <span class="post-thumb-placeholder">No image available</span>
                            @endif
                        </div>
                        <div style="padding: 12px;">
                            <div class="small text-muted">#{{ $post->id }} • {{ optional($post->created_at)->format('d M Y h:i A') }}</div>
                            <h4 style="margin: 8px 0 6px;">{{ \Illuminate\Support\Str::limit($post->title, 48) }}</h4>
                            <p class="text-muted small m-b-sm">{{ \Illuminate\Support\Str::limit(strip_tags($post->description), 90) }}</p>
                            <div class="small m-b-sm">
                                <strong>{{ $post->likes_count }}</strong> likes •
                                <strong>{{ $post->comments_count }}</strong> comments
                            </div>
                            <div class="text-right">
                                <button class="btn btn-xs btn-info js-view-post" data-id="{{ $post->id }}"><i class="fa fa-eye"></i> Open</button>
                                <button class="btn btn-xs btn-warning js-edit-post" data-id="{{ $post->id }}"><i class="fa fa-pencil"></i> Edit</button>
                                <button class="btn btn-xs btn-danger js-delete-post" data-id="{{ $post->id }}"><i class="fa fa-trash"></i> Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12">
                    <div class="alert alert-info text-center">No posts found.</div>
                </div>
            @endforelse
            <div class="col-md-12 text-right">{{ $posts->links() }}</div>
        </div>
    </div>

    <div class="modal fade" id="viewPostModal" tabindex="-1">
        <div class="modal-dialog insta-modal-dialog"><div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Post Detail</h4></div>
            <div class="modal-body insta-modal-body" id="viewPostBody"><p class="text-muted" style="padding: 15px;">Loading...</p></div>
        </div></div>
    </div>

    <div class="modal fade" id="createPostModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createPostForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Create Post</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" maxlength="255" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Image</label>
                            <input type="file" class="form-control" name="thumbnail" accept="image/*">
                            <p class="help-block m-b-none">Optional. JPG/PNG/WEBP up to 5MB.</p>
                        </div>
                        <div class="form-group m-b-none">
                            <label>Tags</label>
                            <input type="text" class="form-control" name="tags" placeholder="fitness, cardio, wellness">
                            <p class="help-block m-b-none">Optional. Comma-separated tags.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="submitCreatePostBtn">Publish</button>
                        <button type="button" class="btn btn-white" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPostModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editPostForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="editPostId">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Post</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" id="editPostTitle" maxlength="255" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="editPostDescription" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Current image</label>
                            <div id="editPostCurrentImage" class="text-muted small">No image</div>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="remove_thumbnail" id="editPostRemoveImage" value="1"> Remove current image</label>
                        </div>
                        <div class="form-group">
                            <label>New image (optional)</label>
                            <input type="file" class="form-control" name="thumbnail" accept="image/*">
                        </div>
                        <div class="form-group m-b-none">
                            <label>Tags</label>
                            <input type="text" class="form-control" name="tags" id="editPostTags" placeholder="fitness, cardio, wellness">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="submitEditPostBtn">Update</button>
                        <button type="button" class="btn btn-white" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
$(function () {
    let currentPostId = null;
    window.initUiEnhancements(document);
    $('#postFilters').on('shown.bs.collapse hidden.bs.collapse', function () {
        $('#togglePostFilters').html('<i class="fa fa-filter"></i> ' + ($(this).hasClass('in') ? 'Hide filters' : 'Show filters'));
    });

    $(document).on('click', '#openCreatePostModal', function () {
        $('#createPostForm')[0].reset();
        $('#createPostModal').modal('show');
    });

    $(document).on('click', '.js-edit-post', function () {
        const id = $(this).data('id');
        $('#editPostForm')[0].reset();
        $('#editPostId').val(id);
        $('#editPostCurrentImage').html('<span class="text-muted">Loading...</span>');
        $.get("{{ url('admin/posts') }}/" + id, function (res) {
            const p = res.data || {};
            $('#editPostTitle').val(p.title || '');
            $('#editPostDescription').val(p.description || '');
            $('#editPostTags').val((p.tags || []).map(function (t) { return t.name; }).join(', '));
            if (p.thumbnail) {
                const src = thumbUrl(p.thumbnail);
                $('#editPostCurrentImage').html('<img src="' + src + '" alt="Current image" style="max-width:120px;max-height:120px;border-radius:6px;">');
            } else {
                $('#editPostCurrentImage').html('<span class="text-muted small">No image</span>');
            }
            $('#editPostModal').modal('show');
        }).fail(function () {
            toastr.error('Unable to load post for edit');
        });
    });

    $('#createPostForm').on('submit', function (e) {
        e.preventDefault();
        const $btn = $('#submitCreatePostBtn');
        const fd = new FormData(this);
        $btn.prop('disabled', true).text('Publishing...');
        $.ajax({
            url: "{{ route('admin.posts.store') }}",
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false
        }).done(function (res) {
            toastr.success(res.message || 'Post created');
            $('#createPostModal').modal('hide');
            location.reload();
        }).fail(function (xhr) {
            const err = xhr.responseJSON || {};
            if (err.errors) {
                const firstField = Object.keys(err.errors)[0];
                toastr.error(err.errors[firstField][0] || 'Validation failed');
            } else {
                toastr.error(err.message || 'Unable to create post');
            }
        }).always(function () {
            $btn.prop('disabled', false).text('Publish');
        });
    });

    $('#editPostForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#editPostId').val();
        if (!id) return;
        const $btn = $('#submitEditPostBtn');
        const fd = new FormData(this);
        fd.append('_method', 'PUT');
        $btn.prop('disabled', true).text('Updating...');
        $.ajax({
            url: "{{ url('admin/posts') }}/" + id,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false
        }).done(function (res) {
            toastr.success(res.message || 'Post updated');
            $('#editPostModal').modal('hide');
            location.reload();
        }).fail(function (xhr) {
            const err = xhr.responseJSON || {};
            if (err.errors) {
                const firstField = Object.keys(err.errors)[0];
                toastr.error(err.errors[firstField][0] || 'Validation failed');
            } else {
                toastr.error(err.message || 'Unable to update post');
            }
        }).always(function () {
            $btn.prop('disabled', false).text('Update');
        });
    });

    function thumbUrl(path) {
        if (!path) return '';
        if (path.indexOf('http://') === 0 || path.indexOf('https://') === 0) return path;
        return "{{ asset('storage') }}/" + String(path).replace(/^\/+/, '');
    }

    function escHtml(text) {
        return String(text || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    $(document).on('click', '.js-view-post', function () {
        const id = $(this).data('id');
        currentPostId = id;
        $('#viewPostModal').modal('show');
        $('#viewPostBody').html('<p class="text-muted" style="padding: 15px;">Loading...</p>');

        $.get("{{ url('admin/posts') }}/" + id, function (res) {
            const p = res.data || {};
            const meta = res.meta || {};
            const image = thumbUrl(p.thumbnail);
            const author = p.user ? ((p.user.first_name || '') + ' ' + (p.user.last_name || '')).trim() : 'Unknown user';
            const likes = p.likes_count || 0;
            const tags = (p.tags || []).map(function (t) { return '#' + escHtml(t.name); }).join(' ');
            const likeBtnClass = meta.liked_by_current_user ? 'btn-danger' : 'btn-default';
            const likeBtnText = meta.liked_by_current_user ? 'Unlike' : 'Like';
            const likedBy = (p.likes || []).slice(0, 5).map(function (l) {
                const u = l.user || {};
                const name = ((u.first_name || '') + ' ' + (u.last_name || '')).trim() || u.email || 'User';
                return escHtml(name);
            }).join(', ');

            const commentsHtml = (p.comments || []).map(function (c) {
                const u = c.user || {};
                const name = ((u.first_name || '') + ' ' + (u.last_name || '')).trim() || u.email || 'User';
                return '<div class="insta-comment-item">' +
                    '<strong>' + escHtml(name) + '</strong> ' +
                    '<span>' + escHtml(c.comment || '') + '</span>' +
                '</div>';
            }).join('') || '<p class="text-muted">No comments yet.</p>';

            $('#viewPostBody').html(
                '<div class="insta-layout">' +
                    '<div class="insta-left">' +
                        (image ? '<img src="' + image + '" alt="Post image">' : '<span class="text-muted">No image</span>') +
                    '</div>' +
                    '<div class="insta-right">' +
                        '<div class="insta-right-head">' +
                            '<div><strong>' + escHtml(author) + '</strong></div>' +
                            '<div class="small text-muted">' + (p.created_at || '') + '</div>' +
                            '<div style="margin-top:8px;"><strong>' + likes + '</strong> likes</div>' +
                            (likedBy ? '<div class="small text-muted">Liked by ' + likedBy + '</div>' : '') +
                        '</div>' +
                        '<div class="insta-comments">' +
                            '<div class="m-b-sm"><strong>' + escHtml(p.title || '') + '</strong></div>' +
                            '<div class="small text-muted m-b-sm">' + escHtml(p.description || '') + '</div>' +
                            (tags ? '<div class="small text-primary m-b-sm">' + tags + '</div>' : '') +
                            '<hr style="margin:10px 0;">' +
                            commentsHtml +
                        '</div>' +
                        '<div class="insta-right-foot">' +
                            '<div class="m-b-sm">' +
                                '<button class="btn btn-xs ' + likeBtnClass + ' js-like-post" data-post-id="' + p.id + '"><i class="fa fa-heart"></i> ' + likeBtnText + '</button>' +
                                ' <button class="btn btn-xs btn-warning js-edit-post" data-id="' + p.id + '"><i class="fa fa-pencil"></i> Edit</button>' +
                                ' <span class="small text-muted"><strong id="modalLikeCount">' + likes + '</strong> likes</span>' +
                            '</div>' +
                            '<div><strong>' + (p.comments_count || 0) + '</strong> comments</div>' +
                            '<div class="input-group input-group-sm m-t-sm">' +
                                '<input type="text" class="form-control" id="newCommentText" placeholder="Write a comment...">' +
                                '<span class="input-group-btn"><button class="btn btn-primary" id="sendNewCommentBtn">Post</button></span>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
        }).fail(function (xhr) {
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to load post';
            $('#viewPostBody').html('<div class="alert alert-danger" style="margin:15px;">' + msg + '</div>');
        });
    });

    $(document).on('click', '.js-like-post', function () {
        const postId = $(this).data('post-id');
        const $btn = $(this);
        $.post("{{ url('admin/posts') }}/" + postId + "/like")
            .done(function (res) {
                $('#modalLikeCount').text(res.likes_count || 0);
                if (res.liked) {
                    $btn.removeClass('btn-default').addClass('btn-danger').html('<i class="fa fa-heart"></i> Unlike');
                } else {
                    $btn.removeClass('btn-danger').addClass('btn-default').html('<i class="fa fa-heart"></i> Like');
                }
            })
            .fail(function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to like post';
                toastr.error(msg);
            });
    });

    $(document).on('click', '#sendNewCommentBtn', function () {
        if (!currentPostId) return;
        const text = $.trim($('#newCommentText').val());
        if (!text) {
            toastr.warning('Please enter comment text');
            return;
        }
        $.post("{{ url('admin/posts') }}/" + currentPostId + "/comments", { comment: text })
            .done(function (res) {
                toastr.success(res.message || 'Comment added');
                $('#newCommentText').val('');
                $('.js-view-post[data-id="' + currentPostId + '"]').trigger('click');
            })
            .fail(function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to add comment';
                toastr.error(msg);
            });
    });

    $(document).on('click', '.js-delete-post', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this post?')) return;
        $.ajax({ url: "{{ url('admin/posts') }}/" + id, method: 'DELETE' })
            .done(function (res) { toastr.success(res.message || 'Deleted'); location.reload(); })
            .fail(function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Delete failed';
                toastr.error(msg);
            });
    });
});
</script>
@endsection
