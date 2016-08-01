@extends('layouts.app')
@section('title','Twitter')
@section('content')
    <div class="wrapper">
        @include('components.navigation')
        @include('components.sidebar')

        <div class="content-wrapper">
            <section class="content">
                <div class="row">
                    <div class="col-md-3">

                        <!-- Profile Image -->
                        <div class="box box-primary">
                            <div class="box-body box-profile">
                                <img class="profile-user-img img-responsive img-circle"
                                     src="{{$me[0]->user->profile_image_url}}" alt="User profile picture">
                                <h3 class="profile-username text-center">{{$me[0]->user->name}}</h3>
                                <ul class="list-group list-group-unbordered">
                                    <li class="list-group-item">
                                        <b>Followers</b> <a class="pull-right">{{ $me[0]->user->followers_count }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Friends</b> <a class="pull-right">{{ $me[0]->user->friends_count }}</a>
                                    </li>

                                    <li class="list-group-item">
                                        <b>Favourites</b> <a class="pull-right">{{ $me[0]->user->favourites_count }}</a>
                                    </li>

                                    <li class="list-group-item">
                                        <b>Statuses</b> <a class="pull-right">{{ $me[0]->user->statuses_count }}</a>
                                    </li>

                                    <li class="list-group-item">
                                        <b>Listed</b> <a class="pull-right">{{ $me[0]->user->listed_count }}</a>
                                    </li>

                                </ul>

                            </div><!-- /.box-body -->
                        </div><!-- /.box -->

                        <!-- About Me Box -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Description</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <strong><i class="fa fa-book margin-r-5"></i>{{$me[0]->user->description}}</strong>

                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    </div><!-- /.col -->
                    <div class="col-md-9">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#activity" data-toggle="tab">Home</a></li>
                                <li><a href="#replies" data-toggle="tab">Replies</a></li>
                                <li><a href="#me" data-toggle="tab">Me</a></li>


                            </ul>
                            <div class="tab-content">
                                <div class="active tab-pane" id="activity">
                                    <!-- Post -->
                                    @foreach($tw as $t=>$fields)
                                        <div class="post">
                                            <div class="user-block">
                                                <img class="img-circle img-bordered-sm"
                                                     src="{{$fields->user->profile_image_url}}" alt="user image">
                        <span class='username'>
                          <a href="#">{{$fields->user->name}}</a>
                            <a href='#' class='pull-right btn-xs btn btn-primary'><i class='fa fa-retweet'></i> Retweet</a>
                        </span>
                                                <span class='description'>{{$fields->user->created_at}}</span>
                                            </div><!-- /.user-block -->
                                            <p>
                                                {{$fields->text}}
                                                <br>

                                            </p>
                                            <div class="row" style="padding-left: 10px"><img
                                                        src="/images/optimus/social/twlove.gif"> {{ $fields->favorite_count }} <img
                                                        style="margin-left: 10px" height="20" width="20"
                                                        src="/images/optimus/social/retweet.png"> {{ $fields->retweet_count }}</div>

                                            &nbsp;Mentions :
                                            @foreach($fields->entities->user_mentions as $mNo => $mentions)

                                                <a href="http://twitter.com/{{$mentions->screen_name}}" target="_blank"><span
                                                            class="badge bg-aqua">{{$mentions->name}}</span></a>
                                            @endforeach
                                        </div><!-- /.post -->



                                @endforeach
                                <!-- Post -->


                                </div><!-- /.tab-pane -->
                                {{-- my activities start --}}


                                {{--replies start--}}

                                <div class="tab-pane" id="replies">
                                    @foreach($twRep as $no => $obj)
                                        <div class="box box-widget widget-user-2">
                                            <!-- Add the bg color to the header using any of the bg-* classes -->
                                            <div class="widget-user-header bg-aqua-active">
                                                <div class="widget-user-image">
                                                    <img class="img-circle" src="{{ $obj->user->profile_image_url }}"
                                                         alt="User Avatar">
                                                </div>
                                                <!-- /.widget-user-image -->
                                                <h3 class="widget-user-username">{{ $obj->user->name }}</h3>
                                                <h5 class="widget-user-desc"> {{$obj->user->description}}</h5>
                                            </div>
                                            <div class="box-footer no-padding">
                                                <ul class="nav nav-stacked">
                                                    <li>

                                                        <div class="row">
                                                            <div class="col-sm-3 border-right">
                                                                <div class="description-block">
                                                                    <h5 class="description-header">{{ $obj->user->followers_count }}</h5>
                                                                    <span class="description-text">FOLLOWERS</span>
                                                                </div>
                                                                <!-- /.description-block -->
                                                            </div>
                                                            <!-- /.col -->
                                                            <div class="col-sm-2 border-right">
                                                                <div class="description-block">
                                                                    <h5 class="description-header">{{ $obj->user->friends_count }}</h5>
                                                                    <span class="description-text">FRIENDS</span>
                                                                </div>
                                                                <!-- /.description-block -->
                                                            </div>
                                                            <!-- /.col -->

                                                            <div class="row">
                                                                <div class="col-sm-2 border-right">
                                                                    <div class="description-block">
                                                                        <h5 class="description-header">{{ $obj->user->favourites_count }}</h5>
                                                                        <span class="description-text">FAVOURITES</span>
                                                                    </div>
                                                                    <!-- /.description-block -->
                                                                </div>
                                                                <!-- /.col -->
                                                                <div class="col-sm-2 border-right">
                                                                    <div class="description-block">
                                                                        <h5 class="description-header">{{ $obj->user->statuses_count }}</h5>
                                                                        <span class="description-text">STATUSES</span>
                                                                    </div>
                                                                    <!-- /.description-block -->
                                                                </div>


                                                                <div class="col-sm-2 border-right">
                                                                    <div class="description-block">
                                                                        <h5 class="description-header">{{ $obj->user->listed_count }}</h5>
                                                                        <span class="description-text">LISTED</span>
                                                                    </div>
                                                                    <!-- /.description-block -->
                                                                </div>


                                                            </div>
                                                        </div>

                                                    </li>
                                                    <li>
                                                        <h3><p>{{$obj->text}}</p></h3>
                                                    </li>

                                                    <li>
                                                        <div class="row">
                                                            <img style="padding-left: 10px"
                                                                 src="/images/optimus/social/twlove.gif"> {{$obj->favorite_count}} <img
                                                                    style="margin-left: 10px" height="20" width="20"
                                                                    src="/images/optimus/social/retweet.png"> {{ $obj->retweet_count }}

                                                            &nbsp; Mentions :
                                                            @foreach($obj->entities->user_mentions as $mNo => $mentions)

                                                                <a href="http://twitter.com/{{$mentions->screen_name}}"
                                                                   target="_blank"><span
                                                                            class="badge bg-aqua">{{$mentions->name}}</span></a>
                                                            @endforeach
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{--replies end--}}

                                <div class="tab-pane" id="me">
                                    @foreach($me as $t=>$f)
                                        <div class="post">
                                            <div class="user-block">
                                                <img class="img-circle img-bordered-sm"
                                                     src="{{$f->user->profile_image_url}}" alt="user image">
                        <span class='username'>
                          <a href="#">{{$f->user->name}}</a>
                          <div data-id="{{$f->id}}" class="optimustwdel"><a class='pull-right btn-box-tool'><i
                                          class='fa fa-times'></i></a></div>
                        </span>
                                                <span class='description'>{{$f->user->created_at}}</span>
                                            </div><!-- /.user-block -->
                                            <p>
                                                {{$f->text}}
                                                <br>

                                            </p>
                                            <div class="row" style="padding-left: 10px"><img
                                                        src="/images/optimus/social/twlove.gif"> {{ $f->favorite_count }} <img
                                                        style="margin-left: 10px" height="20" width="20"
                                                        src="/images/optimus/social/retweet.png"> {{ $f->retweet_count }}</div>

                                            &nbsp; Mentions :
                                            @foreach($f->entities->user_mentions as $mNo => $mentions)

                                                <a href="http://twitter.com/{{$mentions->screen_name}}" target="_blank"><span
                                                            class="badge bg-aqua">{{$mentions->name}}</span></a>
                                            @endforeach
                                        </div><!-- /.post -->
                                    @endforeach
                                </div>

                                {{--my activities end--}}
                                <!-- /.tab-pane -->
                            </div><!-- /.tab-content -->
                        </div><!-- /.nav-tabs-custom -->
                    </div><!-- /.col -->
                </div><!-- /.row -->

            </section>
        </div>
        @include('components.footer')
    </div>
@endsection

@section('css')
    <script src="opt/sweetalert.min.js"></script>
    <link rel="stylesheet" type="text/css" href="opt/sweetalert.css">
@endsection