@extends(config('laravelusers.laravelUsersBladeExtended'))

@section('template_title')
    {!! trans('laravelusers::laravelusers.showing-user', ['name' => $user->name]) !!}
@endsection

@section('template_linked_css')
    @if(config('laravelusers.enabledDatatablesJs'))
        <link rel="stylesheet" type="text/css" href="{{ config('laravelusers.datatablesCssCDN') }}">
    @endif
    @if(config('laravelusers.fontAwesomeEnabled'))
        <link rel="stylesheet" type="text/css" href="{{ config('laravelusers.fontAwesomeCdn') }}">
    @endif
    @include('laravelusers::partials.styles')
    @include('laravelusers::partials.bs-visibility-css')
@endsection

@section('content')
    <div class="container-fluid">
        @if(config('laravelusers.enablePackageBootstapAlerts'))
            <div class="row">
                <div class="col-lg-12">
                    @include('laravelusers::partials.form-status')
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            {!! trans('laravelusers::laravelusers.showing-user-title', ['name' => $user->name]) !!}
                            <div class="float-right">
                                <a href="{{ route('users.index') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left" title="{!! trans('laravelusers::laravelusers.tooltips.back-users') !!}">
                                    @if(config('laravelusers.fontAwesomeEnabled'))
                                        <i class="fas fa-fw fa-reply-all" aria-hidden="true"></i>
                                    @endif
                                    {!! trans('laravelusers::laravelusers.buttons.back-to-users') !!}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h4 class="text-muted text-center">
                            {{ $user->name }}
                        </h4>
                        @if($user->email)
                            <p class="text-center" data-toggle="tooltip" data-placement="top" title="{!! trans('laravelusers::laravelusers.tooltips.email-user', ['user' => $user->email]) !!}">
                                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                            </p>
                        @endif
                        <div class="row mb-4">
                            <div class="col-3 offset-3 col-sm-4 offset-sm-2 col-md-4 offset-md-2 col-lg-3 offset-lg-3">
                                <a href="/users/{{$user->id}}/edit" class="btn btn-block btn-md btn-warning">
                                    {!! trans('laravelusers::laravelusers.buttons.edit-user') !!}
                                </a>
                            </div>
                            <div class="col-3 col-sm-4 col-md-4 col-lg-3">
                                <form method="POST" action="{{ url('users/' . $user->id) }}" class="form-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-md btn-block" data-toggle="modal" data-target="#confirmDelete" data-title="Delete User" data-message="Are you sure you want to delete this user?">
                                        {{ trans('laravelusers::laravelusers.buttons.delete-user') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-4 col-sm-3">
                                        <strong>
                                            {!! trans('laravelusers::laravelusers.show-user.id') !!}
                                        </strong>
                                    </div>
                                    <div class="col-8 col-sm-9">
                                        {{ $user->id }}
                                    </div>
                                </div>
                            </li>
                            @if ($user->name)
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-4 col-sm-3">
                                            <strong>
                                                {!! trans('laravelusers::laravelusers.show-user.name') !!}
                                            </strong>
                                        </div>
                                        <div class="col-8 col-sm-9">
                                            {{ $user->name }}
                                        </div>
                                    </div>
                                </li>
                            @endif
                            @if ($user->email)
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-12 col-sm-3">
                                            <strong>
                                                {!! trans('laravelusers::laravelusers.show-user.email') !!}
                                            </strong>
                                        </div>
                                        <div class="col-12 col-sm-9">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </li>
                            @endif

                            <!-- Membership Information -->
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-4 col-sm-3">
                                        <strong>
                                            Stav členství
                                        </strong>
                                    </div>
                                    <div class="col-8 col-sm-9">
                                        @if($user->hasActiveMembership())
                                            <span class="text-success">
                                                <i class="fas fa-check-circle fa-lg"></i>
                                                Aktivní člen
                                            </span>
                                        @elseif($user->hasPendingMembership())
                                            <span class="text-warning">
                                                <i class="fas fa-clock fa-lg"></i>
                                                Čeká na schválení
                                            </span>
                                        @else
                                            <span class="text-danger">
                                                <i class="fas fa-times-circle fa-lg"></i>
                                                Neaktivní
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-4 col-sm-3">
                                        <strong>
                                            Platné do
                                        </strong>
                                    </div>
                                    <div class="col-8 col-sm-9">
                                        {{ $user->membership_expiry }}
                                    </div>
                                </div>
                            </li>

                            @if(config('laravelusers.rolesEnabled'))
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-4 col-sm-3">
                                            <strong>
                                                {{ trans('laravelusers::laravelusers.show-user.labelRole') }}
                                            </strong>
                                        </div>
                                        <div class="col-8 col-sm-9">
                                            @foreach ($user->roles as $user_role)
                                                @if ($user_role->name == 'User')
                                                    @php $labelClass = 'primary' @endphp
                                                @elseif ($user_role->name == 'Admin')
                                                    @php $labelClass = 'warning' @endphp
                                                @elseif ($user_role->name == 'Unverified')
                                                    @php $labelClass = 'danger' @endphp
                                                @else
                                                    @php $labelClass = 'default' @endphp
                                                @endif
                                                <span class="badge badge-{{$labelClass}}">{{ $user_role->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-12 col-sm-3">
                                            <strong>
                                                {!! trans_choice('laravelusers::laravelusers.show-user.labelAccessLevel', 1) !!}
                                            </strong>
                                        </div>
                                        <div class="col-12 col-sm-9">
                                            @if($user->level() >= 5)
                                                <span class="badge badge-primary margin-half margin-left-0">5</span>
                                            @endif
                                            @if($user->level() >= 4)
                                                <span class="badge badge-info margin-half margin-left-0">4</span>
                                            @endif
                                            @if($user->level() >= 3)
                                                <span class="badge badge-success margin-half margin-left-0">3</span>
                                            @endif
                                            @if($user->level() >= 2)
                                                <span class="badge badge-warning margin-half margin-left-0">2</span>
                                            @endif
                                            @if($user->level() >= 1)
                                                <span class="badge badge-default margin-half margin-left-0">1</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endif
                            @if ($user->created_at)
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-4 col-sm-3">
                                            <strong>
                                                {!! trans('laravelusers::laravelusers.show-user.created') !!}
                                            </strong>
                                        </div>
                                        <div class="col-8 col-sm-9">
                                            {{ $user->created_at }}
                                        </div>
                                    </div>
                                </li>
                            @endif
                            @if ($user->updated_at)
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-4 col-sm-3">
                                            <strong>
                                                {!! trans('laravelusers::laravelusers.show-user.updated') !!}
                                            </strong>
                                        </div>
                                        <div class="col-8 col-sm-9">
                                            {{ $user->updated_at }}
                                        </div>
                                    </div>
                                </li>
                            @endif
                        </ul>

                        <!-- Detailed Membership Information -->
                        @if($user->hasActiveMembership())
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="card-title">Informace o členství</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Stav členství:</strong><br>
                                        <span class="text-success">
                                            <i class="fas fa-check-circle fa-lg"></i>
                                            Aktivní člen
                                        </span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Platné do:</strong><br>
                                        {{ $user->membership_expiry }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Členské ID:</strong><br>
                                        {{ $user->activeMembership()->id }}
                                    </div>
                                </div>

                                @if($user->hasActiveMembership())
                                <div class="mt-3">
                                    @php $membership = $user->activeMembership(); @endphp
                                    <small class="text-muted">
                                        Variabilní symbol: {{ $membership->payment_reference }} |
                                        Aktivováno: {{ $membership->payment_verified_at->format('d.m.Y H:i') }}
                                    </small>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('laravelusers::modals.modal-delete')
@endsection

@section('template_scripts')
    @include('laravelusers::scripts.delete-modal-script')
    @if(config('laravelusers.tooltipsEnabled'))
        @include('laravelusers::scripts.tooltips')
    @endif
@endsection
