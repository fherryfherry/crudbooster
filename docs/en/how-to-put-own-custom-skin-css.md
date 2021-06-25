## How To Put Your Own Custom Skin for CB Theme

1. Open the privileges menu and select a specific privilege.
After that, at Theme color option, select `Custom Skin` .
2. Go to your application assets directory and create the following file
   `css/custom-skin.css`
3. Define your own styles for your new exciting CB app. Remember CB is based
on AdminLTE so basically all css you create should be based on that template.
   Keep in mind that every class you create should be preceded by `.custom-skin`
   in order for CB to apply it in your default templates.
   The following is a full example of the CB template UI customization:
```css
.custom-skin .dropdown-menu-action {
  left: -135px;
}
 @media screen and (min-width:767px) { 
 .custom-skin .button_action{
  /*max-width: 15vw;*/
 }
}
 .custom-skin p a{
  color:#fff;
}
.custom-skin table > tbody > tr > td > a{
  color:#4070FA;
}
 .custom-skin h1,h2,h3,h4{
  font-family: Raleway, sans-serif;
  font-weight: 500;
  letter-spacing: 1px;
}
.custom-skin p{
  font-family: Raleway, sans-serif;
  font-weight:400; 
}
.custom-skin .content-header i{
}
.custom-skin .content-header h1{
  color:#fff;
}
.custom-skin .main-header .navbar {
  background-color: #000032;
}
.custom-skin .main-header .navbar .nav > li > a {
    color: #ffffff;
}
.custom-skin .main-header .navbar .nav > li > a:hover,
.custom-skin .main-header .navbar .nav > li > a:active,
.custom-skin .main-header .navbar .nav > li > a:focus,
.custom-skin .main-header .navbar .nav .open > a,
.custom-skin .main-header .navbar .nav .open > a:hover,
.custom-skin .main-header .navbar .nav .open > a:focus,
.custom-skin .main-header .navbar .nav > .active > a {
    background: rgba(0, 0, 0, 0.1);
    color: #f6f6f6;
}
.custom-skin .main-header .navbar .navbar-toggle{
    border-color: #4070FA;
}
.custom-skin .main-header .navbar .navbar-toggle .icon-bar{
    background-color: #17cffb;
}
.custom-skin .main-header .navbar .sidebar-toggle {
    color: #ffffff;
}
.custom-skin .main-header .navbar .sidebar-toggle:hover {
    color: #f6f6f6;
    background: rgba(0, 0, 0, 0.1);
}
.custom-skin .main-header .navbar .sidebar-toggle {
    color: #fff;
}
.custom-skin .main-header .navbar .sidebar-toggle:hover {
    background-color: #000032;
}
@media (max-width: 767px) {
    .custom-skin .main-header .navbar .dropdown-menu li.divider {
        background-color: rgba(255, 255, 255, 0.1);
    }
    .custom-skin .main-header .navbar .dropdown-menu li a {
        color: #fff;
    }
    .custom-skin .main-header .navbar-custom-menu a.btn-danger{
        background-color: red;
    }
    .custom-skin .main-header .navbar .dropdown-menu li a:hover {
        background: #000032;
    }
}
.custom-skin .main-header .navbar-custom-menu a.btn-default[title="Lock Screen"]{
    display:none;
}
.custom-skin .main-header .logo {
    background-color: #000032;
    color: #ffffff;
    border-bottom: 0 solid transparent;
}
.custom-skin .main-header .logo:hover {
    background-color: #357ca5;
}
.custom-skin .main-header li.user-header {
    background-color:  #000032;
}
.custom-skin .content-header {
    /*background-color: #0089ff;*/
    padding-bottom:15px;
}
.custom-skin .content-header>.breadcrumb {
    background-color: white;
    border-radius: 5px;
    display: none;

}
.custom-skin .wrapper,
.custom-skin .main-sidebar,
.custom-skin .left-side {
    background-color: #000032;
}
.custom-skin .user-panel > .info,
.custom-skin .user-panel > .info > a {
    color: #fff;
}
.custom-skin .sidebar-menu > li.header {
    color: #4b646f;
    background: #000032;
}
.custom-skin .sidebar-menu > li > a {
    border-left: 3px solid transparent;
}
.custom-skin .sidebar-menu > li:hover > a,
.custom-skin .sidebar-menu > li.active > a,
.custom-skin .sidebar-menu > li.menu-open > a {
    color: #ffffff;
    background: linear-gradient(104deg, rgba(64,112,250,0.5) 0%, rgba(64,112,250,0.1) 100%);
}
.custom-skin .sidebar-menu > li.active > a {
    border-left-color: #3c8dbc;
}
.custom-skin .sidebar-menu > li > .treeview-menu {
    margin: 0 1px;
    background: #2c3b41;
}
.custom-skin .sidebar a {
    color: #b8c7ce;
}
.custom-skin .sidebar a:hover {
    text-decoration: none;
}
.custom-skin .sidebar-menu .treeview-menu > li > a {
    color: #8aa4af;
}
.custom-skin .sidebar-menu .treeview-menu > li.active > a,
.custom-skin .sidebar-menu .treeview-menu > li > a:hover {
    color: #ffffff;
}
.custom-skin .sidebar-form {
    border-radius: 3px;
    border: 1px solid #374850;
    margin: 10px 10px;
}
.custom-skin .sidebar-form input[type="text"],
.custom-skin .sidebar-form .btn {
    box-shadow: none;
    background-color: #374850;
    border: 1px solid transparent;
    height: 35px;
}
.custom-skin .sidebar-form input[type="text"] {
    color: #666;
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
}
.custom-skin .sidebar-form input[type="text"]:focus,
.custom-skin .sidebar-form input[type="text"]:focus + .input-group-btn .btn {
    background-color: #fff;
    color: #666;
}
.custom-skin .sidebar-form input[type="text"]:focus + .input-group-btn .btn {
    border-left-color: #fff;
}
.custom-skin .sidebar-form .btn {
    color: #999;
    border-top-left-radius: 0;
    border-top-right-radius: 2px;
    border-bottom-right-radius: 2px;
    border-bottom-left-radius: 0;
}
.custom-skin.layout-top-nav .main-header > .logo {
    background-color: #3c8dbc;
    color: #ffffff;
    border-bottom: 0 solid transparent;
}
.custom-skin.layout-top-nav .main-header > .logo:hover {
    background-color: #3b8ab8;
}
.custom-skin .content-wrapper, .right-side {
    min-height: 100%;
    background:linear-gradient(to bottom, rgb(31, 37, 78), rgb(21, 21, 84));
    z-index: 800;
}
.custom-skin .box{
    box-shadow:0 5px 20px rgba(4, 43, 40, 0.57), 0 3px 6px rgba(0, 0, 0, 0.10);
    border-top:none;border-radius: 6px;
}
.custom-skin .box.box-solid.box-info>.box-header {
    color: #fff;
    background: #00c0ef;
    background-color: #00c0ef;
}
.custom-skin .box .box-header{
    padding: 10px 10px 0px 10px;
}
.custom-skin .table>tbody>tr.active>td, .table>tbody>tr.active>th, .table>tbody>tr>td.active, .table>tbody>tr>th.active, .table>tfoot>tr.active>td, .table>tfoot>tr.active>th, .table>tfoot>tr>td.active, .table>tfoot>tr>th.active, .table>thead>tr.active>td, .table>thead>tr.active>th, .table>thead>tr>td.active, .table>thead>tr>th.active {
    background-color:#fff;
}

.custom-skin .table>thead>tr>th>a{
    color: #4070FA;
}

.custom-skin .table-hover tbody tr:hover td, .table-hover tbody tr:hover th{
    background-color: #3c8dbc;
    /*font-size: 1em;
  letter-spacing: 1px;*/
    color: white ;
    border-color:#3c8dbc;
}

.custom-skin .table-hover tbody tr:hover .text-primary, .table-hover tbody tr:hover .text-success,.table-hover tbody tr:hover .text-danger{
    color:white
}

.custom-skin .table-hover tbody tr:hover a,.table-hover tbody tr:hover button{
    /* font-size:1.2em;*/
}
.custom-skin .table-hover tbody tr:hover input[type=checkbox]{
    /* Double-sized Checkboxes */
    -ms-transform: scale(1.4);
    -moz-transform: scale(1.4);
    -webkit-transform: scale(1.4);
    -o-transform: scale(1.4);
}
.custom-skin .table tbody tr td, .table tbody tr th {
    transition: 0.3s;
}
.custom-skin .btn.btn-primary{
    background-color:  #4070FA;
    border-color:  #4070FA;
}
.custom-skin .btn.btn-primary.active{
    background-color:   #0D48F2;
    border-color:  #A7AAB3;
    box-shadow:0 5px 20px rgba(0, 0, 0, 0.05), 0 3px 6px rgba(0, 0, 0, 0.5);
}
.custom-skin .btn.btn-info{
    background-color:  #5bc0de;
    border-color: #46b8da;
}
.custom-skin .btn.btn-info.active{
    color: #fff;
    background-color: #31b0d5;
    border-color: #269abc;
    box-shadow:0 5px 20px rgba(0, 0, 0, 0.05), 0 3px 6px rgba(0, 0, 0, 0.5);
}
.custom-skin .small-box{
    border-radius:12px;
    box-shadow:0 5px 20px rgba(0, 0, 0, 0.05), 0 3px 6px rgba(0, 0, 0, 0.10);
}
.custom-skin .small-box.bg-white h3{
    color:#4070FA;
}
.custom-skin .small-box.bg-white p {
    font-weight:600;
    font-size: 14px;
    color: #8e8b96;
}
.custom-skin .small-box.bg-white h2 {
    font-size: 20px;
}
.custom-skin .small-box.bg-white .icon{
    color:rgba(226, 236, 239, 0.47);
}
.custom-skin .bg-white{
    background-color: #fff !important;
}
.custom-skin .bg-red{
    background-color:#fa405a !important;
}
.custom-skin .bg-green{
    background-color:#1bb744 !important;
}
.custom-skin .bg-aqua{
    background-color: transparent !important;
    background: linear-gradient(104deg, rgb(23, 44, 105) 0%, rgb(21, 66, 195) 100%);
}
.custom-skin .bg-blue{
    background-color: transparent !important;
    background: linear-gradient(104deg, #40bdff 0%,  #0359f7 100%);
}
.custom-skin .small-box.bg-blue{
    box-shadow:0px 8px 15px rgba(64,112,250, 0.3);
}

.custom-skin .bg-grey{
    background-color:transparent !important;
    background: linear-gradient(104deg, #d9eaf5 0%, #f5f5f5 100%);
}
.custom-skin .bg-yellow{
    background-color: #fff !important;
}
.custom-skin .small-box.bg-yellow h3{
    color:#4070FA;
}
.custom-skin .small-box.bg-yellow p {
    font-weight:600;
    font-size: 14px;
    color: #8e8b96;
}
.custom-skin .small-box.bg-yellow .icon{
    color:rgba(226, 236, 239, 0.47);
}
.custom-skin .bg-purple{
    background-color: #9e40e6 !important;
}
.custom-skin .box .no-padding{
    padding:15px !important;
}
.custom-skin .panel > .panel-heading{
    font-size: 1.2em;
}
.custom-skin .panel-default>.panel-heading {
    color:  #ffffff;
    background-color:#000032;
    border-color: #ddd;

}
.custom-skin .panel {
    border-radius:6px;
    box-shadow:0 5px 20px rgba(0, 0, 0, 0.05), 0 3px 6px rgba(0, 0, 0, 0.10);

}
.custom-skin .modal-header {
    color:  #ffffff;
    background-color:#000032;
    border-color: #ddd;
    font-size: 1.2em;
}
.custom-skin .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
    background-color: #eae9e9;
    border-radius: 3px;
    opacity: 1;
}

@media (max-width: 767px){
    .custom-skin .small-box .icon {
        display: block;
    }
    .custom-skin .content-header h1{
        text-align: center;
        font-size: 20px;
        padding-top:7px;
    }
}

.custom-skin button.close{
    opacity: 0.6;
    color: #fff;
}
```

## What's Next
- [How To Put Your Own Javascript](./how-to-put-your-own-javascript.md)

## Table Of Contents
- [Back To Index](./index.md)
