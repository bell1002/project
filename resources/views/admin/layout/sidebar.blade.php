<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('admin_home') }}">Admin Panel</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('admin_home') }}"></a>
        </div>

        <ul class="sidebar-menu">

            <li class="{{ Request::is('admin/home')? 'active' : ']' }}active"><a class="nav-link" href="{{ route('admin_home') }}"><i class="fa fa-pie-chart"></i><span>Dashboard</span></a></li>


            <li class="nav-item dropdown {{ Request::is('admin/amenity/view')  ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fa fa-hand-o-right"></i><span>Hotel Section</span></a>
                <ul class="dropdown-menu">
                    <li class="{{  Request::is('admin/amenity/view') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin_amenity_view') }}"><i class="fa fa-angle-right"></i>Amenities</a></li>
                    <li class="{{  Request::is('admin/room/view') ? 'active' : '' }}"><a class="nav-link" href="{{ route('admin_room_view') }}"><i class="fa fa-angle-right"></i>Rooms</a></li>
                </ul>
            </li>


            <li class="nav-item dropdown active">
                <a href="#" class="nav-link has-dropdown"><i class="fa fa-hand-o-right"></i><span>Pages</span></a>
                <ul class="dropdown-menu">
                    <li class="active"><a class="nav-link" href="{{ route('admin_page_about') }}"><i class="fa fa-angle-right"></i>About</a></li>
                    <li class=""><a class="nav-link" href=""><i class="fa fa-angle-right"></i> Item 2</a></li>
                </ul>
            </li>

            <li class=""><a class="nav-link" href="{{ route('admin_slide_view') }}"><i class="fa fa-sliders"></i> <span>Slide</span></a></li>

            <li class=""><a class="nav-link" href="{{ route('admin_feature_view') }}"><i class="fa fa-sliders"></i> <span>Feature</span></a></li>

            <li class=""><a class="nav-link" href="{{ route('admin_testimonial_view') }}"><i class="fa fa-sliders"></i> <span>Testimonial</span></a></li>

            <li class=""><a class="nav-link" href="{{ route('admin_post_view') }}"><i class="fa fa-sliders"></i> <span>Post</span></a></li>

            <li class=""><a class="nav-link" href="{{ route('admin_photo_view') }}"><i class="fa fa-sliders"></i> <span>Photo Gallery</span></a></li>

            <li class=""><a class="nav-link" href="{{ route('admin_video_view') }}"><i class="fa fa-sliders"></i> <span>Video Gallery</span></a></li>

            <li class=""><a class="nav-link" href="{{ route('admin_faq_view') }}"><i class="fa fa-sliders"></i> <span>Faq</span></a></li>

        </ul>
    </aside>
</div>
