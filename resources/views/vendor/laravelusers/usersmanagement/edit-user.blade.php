@extends(config('laravelusers.laravelUsersBladeExtended'))

@section('template_title')
    {{ trans('laravelusers::laravelusers.editing-user', ['name' => $user->name]) }}
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
    <div class="container">
        @if(config('laravelusers.enablePackageBootstapAlerts'))
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    @include('laravelusers::partials.form-status')
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            {{ trans('laravelusers::laravelusers.editing-user', ['name' => $user->name]) }}
                            <div class="pull-right">
                                <a href="{{ route('users.index') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="top" title="{{ trans('laravelusers::laravelusers.tooltips.back-users') }}">
                                    @if(config('laravelusers.fontAwesomeEnabled'))
                                        <i class="fas fa-fw fa-reply-all" aria-hidden="true"></i>
                                    @endif
                                    {{ trans('laravelusers::laravelusers.buttons.back-to-users') }}
                                </a>
                                <a href="{{ url('/users/' . $user->id) }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left" title="{{ trans('laravelusers::laravelusers.tooltips.back-user') }}">
                                    @if(config('laravelusers.fontAwesomeEnabled'))
                                        <i class="fas fa-fw fa-reply" aria-hidden="true"></i>
                                    @endif
                                    {{ trans('laravelusers::laravelusers.buttons.back-to-user') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('users.update', $user->id) }}" role="form" class="needs-validation">
                            @csrf
                            @method('PUT')

                            <!-- First Name Field -->
                            <div class="form-group has-feedback row {{ $errors->has('first_name') ? ' has-error ' : '' }}">
                                <label for="first_name" class="col-md-3 control-label">Křestní jméno</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" placeholder="First Name" required>
                                        <div class="input-group-append">
                                            <label class="input-group-text" for="first_name">
                                                @if(config('laravelusers.fontAwesomeEnabled'))
                                                    <i class="fa fa-fw fa-user" aria-hidden="true"></i>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                    @if ($errors->has('first_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('first_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Last Name Field -->
                            <div class="form-group has-feedback row {{ $errors->has('last_name') ? ' has-error ' : '' }}">
                                <label for="last_name" class="col-md-3 control-label">Příjmení</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" placeholder="Last Name" required>
                                        <div class="input-group-append">
                                            <label class="input-group-text" for="last_name">
                                                @if(config('laravelusers.fontAwesomeEnabled'))
                                                    <i class="fa fa-fw fa-user" aria-hidden="true"></i>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                    @if ($errors->has('last_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('last_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group has-feedback row {{ $errors->has('email') ? ' has-error ' : '' }}">
                                @if(config('laravelusers.fontAwesomeEnabled'))
                                    <label for="email" class="col-md-3 control-label">{{ trans('laravelusers::forms.create_user_label_email') }}</label>
                                @endif
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <input type="text" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" placeholder="{{ trans('laravelusers::forms.create_user_ph_email') }}">
                                        <div class="input-group-append">
                                            <label for="email" class="input-group-text">
                                                @if(config('laravelusers.fontAwesomeEnabled'))
                                                    <i class="fa fa-fw {{ trans('laravelusers::forms.create_user_icon_email') }}" aria-hidden="true"></i>
                                                @else
                                                    {{ trans('laravelusers::forms.create_user_label_email') }}
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if($rolesEnabled)
                                <div class="form-group has-feedback row {{ $errors->has('role') ? ' has-error ' : '' }}">
                                    @if(config('laravelusers.fontAwesomeEnabled'))
                                        <label for="role" class="col-md-3 control-label">{{ trans('laravelusers::forms.create_user_label_role') }}</label>
                                    @endif
                                    <div class="col-md-9">
                                    <div class="input-group">
                                        <select class="custom-select form-control" name="role[]" id="role" multiple>
                                            @if ($roles)
                                                @foreach($roles as $role)
                                                    @if ($currentRole)
                                                        <option value="{{ $role->id }}" {{ in_array($role->id, $currentRole) ? 'selected="selected"' : '' }}>{{ $role->name }}</option>
                                                    @else
                                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="input-group-append">
                                            <label class="input-group-text" for="role">
                                                @if(config('laravelusers.fontAwesomeEnabled'))
                                                    <i class="{{ trans('laravelusers::forms.create_user_icon_role') }}" aria-hidden="true"></i>
                                                @else
                                                    {{ trans('laravelusers::forms.create_user_label_username') }}
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                    @if ($errors->has('role'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('role') }}</strong>
                                        </span>
                                    @endif
                                    </div>
                                </div>
                            @endif
                            <div class="pw-change-container">
                                <div class="form-group has-feedback row {{ $errors->has('password') ? ' has-error ' : '' }}">
                                    @if(config('laravelusers.fontAwesomeEnabled'))
                                        <label for="password" class="col-md-3 control-label">{{ trans('laravelusers::forms.create_user_label_password') }}</label>
                                    @endif
                                    <div class="col-md-9">
                                        <div class="input-group">
                                            <input type="password" name="password" id="password" class="form-control" placeholder="{{ trans('laravelusers::forms.create_user_ph_password') }}">
                                            <div class="input-group-append">
                                                <label class="input-group-text" for="password">
                                                    @if(config('laravelusers.fontAwesomeEnabled'))
                                                        <i class="fa fa-fw {{ trans('laravelusers::forms.create_user_icon_password') }}" aria-hidden="true"></i>
                                                    @else
                                                        {{ trans('laravelusers::forms.create_user_label_password') }}
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group has-feedback row {{ $errors->has('password_confirmation') ? ' has-error ' : '' }}">
                                    @if(config('laravelusers.fontAwesomeEnabled'))
                                        <label for="password_confirmation" class="col-md-3 control-label">{{ trans('laravelusers::forms.create_user_label_pw_confirmation') }}</label>
                                    @endif
                                    <div class="col-md-9">
                                        <div class="input-group">
                                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="{{ trans('laravelusers::forms.create_user_ph_pw_confirmation') }}">
                                            <div class="input-group-append">
                                                <label class="input-group-text" for="password_confirmation">
                                                    @if(config('laravelusers.fontAwesomeEnabled'))
                                                        <i class="fa fa-fw {{ trans('laravelusers::forms.create_user_icon_pw_confirmation') }}" aria-hidden="true"></i>
                                                    @else
                                                        {{ trans('laravelusers::forms.create_user_label_pw_confirmation') }}
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                        @if ($errors->has('password_confirmation'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6 mb-2">
                                    <a href="#" class="btn btn-outline-secondary btn-block btn-change-pw mt-3" title="{{ trans('laravelusers::forms.change-pw') }}">
                                        <i class="fa fa-fw fa-lock" aria-hidden="true"></i>
                                        <span></span> {{ trans('laravelusers::forms.change-pw') }}
                                    </a>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <button type="button" class="btn btn-success btn-block margin-bottom-1 mt-3 mb-2 btn-save" data-toggle="modal" data-target="#confirmSave" data-title="{{ trans('laravelusers::modals.edit_user__modal_text_confirm_title') }}" data-message="{{ trans('laravelusers::modals.edit_user__modal_text_confirm_message') }}">
                                        @if(config('laravelusers.fontAwesomeEnabled'))
                                            <i class="fa fa-fw fa-save" aria-hidden="true"></i>
                                        @endif
                                        {{ trans('laravelusers::forms.save-changes') }}
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Membership Management Section -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="card-title">Správa členství</h5>
                            </div>
                            <div class="card-body">
                                <!-- Current Membership Status -->
                                <div class="mb-3">
                                    <label class="form-label"><strong>Aktuální stav:</strong></label>
                                    <div>
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

                                <!-- Manual Membership Actions -->
                                <div class="mb-3">
                                    <label class="form-label"><strong>Akce členství:</strong></label>
                                    <div>
                                        @if($user->hasActiveMembership())
                                            <form action="{{ route('administration.payments.reject', $user->activeMembership()) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm"
                                                        onclick="return confirm('Opravdu chcete zrušit členství uživatele {{ $user->name }}?')">
                                                    Zrušit členství
                                                </button>
                                            </form>
                                        @elseif($user->hasPendingMembership())
                                            <form action="{{ route('administration.payments.verify', $user->pendingMembership()) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    Schválit členství
                                                </button>
                                            </form>
                                            <form action="{{ route('administration.payments.reject', $user->pendingMembership()) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Zamítnout členství
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-info btn-sm" disabled>
                                                Žádné členství ke správě
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Membership History -->
                                <div class="mt-3">
                                    <label class="form-label"><strong>Historie členství:</strong></label>
                                    @if($user->memberships->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Typ</th>
                                                        <th>Stav</th>
                                                        <th>Platné do</th>
                                                        <th>Datum schválení</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($user->memberships as $membership)
                                                    <tr>
                                                        <td>{{ $membership->id }}</td>
                                                        <td>
                                                            @if($membership->type === 'premium') Prémiové
                                                            @elseif($membership->type === 'family') Rodinné
                                                            @elseif($membership->type === 'basic') Základní
                                                            @else {{ $membership->type }} @endif
                                                        </td>
                                                        <td>
                                                            @if($membership->status === 'active')
                                                                <span class="text-success">
                                                                    <i class="fas fa-check-circle"></i>
                                                                    Aktivní
                                                                </span>
                                                            @elseif($membership->status === 'pending')
                                                                <span class="text-warning">
                                                                    <i class="fas fa-clock"></i>
                                                                    Čekající
                                                                </span>
                                                            @elseif($membership->status === 'expired')
                                                                <span class="text-secondary">
                                                                    <i class="fas fa-calendar-times"></i>
                                                                    Expirované
                                                                </span>
                                                            @elseif($membership->status === 'cancelled')
                                                                <span class="text-danger">
                                                                    <i class="fas fa-times-circle"></i>
                                                                    Zrušené
                                                                </span>
                                                            @else
                                                                <span class="text-dark">
                                                                    {{ $membership->status }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $membership->expires_at ? $membership->expires_at->format('d.m.Y') : '—' }}</td>
                                                        <td>{{ $membership->payment_verified_at ? $membership->payment_verified_at->format('d.m.Y H:i') : '—' }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">Žádná historie členství.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('laravelusers::modals.modal-save')
    @include('laravelusers::modals.modal-delete')

@endsection

@section('template_scripts')
    @include('laravelusers::scripts.delete-modal-script')
    @include('laravelusers::scripts.save-modal-script')
    @include('laravelusers::scripts.check-changed')
    @if(config('laravelusers.tooltipsEnabled'))
        @include('laravelusers::scripts.tooltips')
    @endif
@endsection
