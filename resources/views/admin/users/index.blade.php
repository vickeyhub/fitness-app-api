@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Users
            </h2>
            <ol class="breadcrumb">
                <li>
                    <a href="index.html">Home</a>
                </li>
                <!-- <li>
                    <a>App Views</a>
                </li> -->
                <li class="active">
                    <strong>Users</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content  animated fadeInRight">
        <!-- Trigger the modal with a button -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Add
            User</button>

        <!-- Modal -->
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h2 class="modal-title">Add User</h2>
                    </div>
                    <div class="modal-body">
                        <div class="contact-box">
                            <div class="row" style="padding:0 30px;">
                                <form class="m-t " role="form" action="index.html">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" class="form-control" required="">
                                    </div>

                                    <div class="form-group">
                                        <label>Date of Joining</label>
                                        <input type="date" class="form-control" required="">
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" required="">
                                    </div>
                                    <div class="form-group">
                                        <label>Profile</label>
                                        <input type="file" class="form-control" required="">
                                    </div>
                                    <div class="form-group">
                                        <label>Mobile No.</label>
                                        <input type="text" class="form-control" required="">
                                    </div>
                                    <div class="form-group">
                                        <label>Gender</label>
                                        <select class="form-control m-b" name="account">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Height</label>
                                        <input type="text" class="form-control" required="">
                                    </div>
                                    <div class="form-group">
                                        <label>Weight</label>
                                        <input type="text" class="form-control" required="">
                                    </div>

                                    <button type="submit" class="btn btn-primary block full-width m-b">Add</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="row m-t-xs">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-content">

                        <!-- <div class="clients-list"> -->
                        <div class="">
                            <div class="tab-content">
                                <div id="editModal" class="modal fade" role="dialog">
                                    <div class="modal-dialog modal-dialog-centered">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h2 class="modal-title">Edit User</h2>
                                            </div>
                                            <div class="modal-body">
                                                <div class="contact-box">
                                                    <div class="row" style="padding:0 30px;">
                                                        <form class="m-t " role="form" action="index.html">
                                                            <div class="form-group">
                                                                <label>Name</label>
                                                                <input type="text" class="form-control" required="">
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Date of Joining</label>
                                                                <input type="date" class="form-control" required="">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Email</label>
                                                                <input type="email" class="form-control" required="">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Profile</label>
                                                                <input type="file" class="form-control" required="">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Mobile No.</label>
                                                                <input type="text" class="form-control"
                                                                    required="">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Gender</label>
                                                                <select class="form-control m-b" name="account">
                                                                    <option>option 1</option>
                                                                    <option>option 2</option>
                                                                    <option>option 3</option>
                                                                    <option>option 4</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Height</label>
                                                                <input type="text" class="form-control"
                                                                    required="">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Weight</label>
                                                                <input type="text" class="form-control"
                                                                    required="">
                                                            </div>
                                                            <button type="submit"
                                                                class="btn btn-primary block full-width m-b">Edit</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div id="tab-1" class="tab-pane active">
                                    <div class="full-height-scroll">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">S.No.</th>
                                                        <th scope="col">Date of Joining</th>
                                                        <th scope="col">Name</th>
                                                        <th scope="col">Email</th>
                                                        <th scope="col">Profile Picture</th>
                                                        <th scope="col">Mobile No</th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($users as $user)
                                                    <tr>
                                                        <td>{{ $user->id }}</td>
                                                        <td>{{ date('d/m/Y', strtotime($user->created_at)) }}</td>
                                                        <td>{{ $user->first_name. ' '.$user->last_name }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>
                                                            @if(optional($user->profile)->profile_picture)
                                                            <a href="profile.html" class="pull-left">
                                                                <img alt="image" class="img-circle-table"
                                                                    src="{{ $user->profile->profile_picture ?? 'N/A' }}">
                                                            </a>
                                                            @else
                                                                No Image Found
                                                            @endif
                                                        </td>
                                                        <td>{{ $user->mobile_number ?? 'Not available' }}</td>
                                                        <td>
                                                            <a href=""><i class="fa fa-eye"
                                                                    aria-hidden="true"></i></a>
                                                        </td>
                                                        <td><a href="" type="button" data-toggle="modal"
                                                                data-target="#editModal"><i class="fa fa-pencil"
                                                                    aria-hidden="true"></i></a>
                                                        </td>
                                                        <td>
                                                            <a href=""><i class="fa fa-trash"
                                                                    aria-hidden="true"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
