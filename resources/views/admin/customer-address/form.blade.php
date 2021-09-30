@extends('admin.layout.master')
@section('city_list','active')
{{-- @section('Role Management','open') --}}
@section('title')
    @lang('admin_action.edit_page_title')
@endsection
@section('page-name')
    @lang('admin_action.edit_page_sub_title')
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">@lang('admin_action.breadcrumb_title')</a>
    </li>
    <li class="breadcrumb-item active">City
    </li>
@endsection
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">

@section('content')


    
    <div class="saleform-wrapper mt-2">
        <div class="container">
            <div class="form-title mb-2">
                <h3>Basic Information</h3>
            </div>
            <div class="saleform-header mb-2">
                <p>Property ID: 100001</p>
                <p>Create Date: July 10, 2020</p>
                <p>Modifieed On: July 17, 2020</p>
            </div>
            <form action="#" method="post" enctype="multipart/form-data">
                  <div class="row">
                      <!-- Type User -->
                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="label-title">User Type</label>
                              <input type="radio" checked="" name="usertype" value="individual" id="individual"> 
                              <label for="individual">Individual</label>
                              <input type="radio" name="usertype" value="developer" id="developer"> 
                              <label for="developer">Developer</label>
                              <input type="radio" name="usertype" value="agency" id="agency"> 
                              <label for="agency">Agency</label>
                              <input type="radio" name="usertype" value="agent" id="agent"> 
                              <label for="agent">Agent</label>
                          </div>
                      </div>
                      <!-- Advertisment Type -->
                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="label-title">Advertisement Type <span>*</span></label>
                              <input type="radio" checked="" name="alert" value="sell" id="sell"> 
                              <label for="sell">Sell</label>
                              <input type="radio" name="alert" value="rent" id="rent"> 
                              <label for="rent">Rent</label>
                              <input type="radio" name="alert" value="roommate" id="roommate"> 
                              <label for="roommate">Roommate</label>
                          </div>
                      </div>
                      <!-- User Name -->
                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="label-title">User Name</label>
                              <input type="text" class="form-control" name="user_name" id="user_name" placeholder="User Name">
                          </div>
                      </div>
                      <!-- Property Type -->
                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="label-title">Property Type</label>
                              <select class="form-control" class="form-control propertyType" id="propertyType">
                                    <option>Select Property Type</option>
                                    <option>Select Property Type</option>
                                    <option>Select Property Type</option>
                                    <option>Select Property Type</option>
                              </select>
                          </div>
                      </div>
                      <!-- City -->
                      <div class="col-md-6">
                         <div class="form-group">
                              <label class="label-title">City <span>*</span></label>
                              <select class="form-control" class="form-control city" id="city" placeholder="Select City">
                                    <option>Select City</option>
                                    <option>Select City</option>
                                    <option>Select City</option>
                                    <option>Select City</option>
                              </select>
                         </div>
                      </div>
                      <!-- Area (Based on city) -->
                      <div class="col-md-6">
                         <div class="form-group">
                              <label class="label-title">Area (Based on City) <span>*</span></label>
                              <select class="form-control" class="form-control area" id="area">
                                    <option>Select Area</option>
                                    <option>Select Area</option>
                                    <option>Select Area</option>
                                    <option>Select Area</option>
                              </select>
                         </div>
                      </div>
                      <!-- Address -->
                      <div class="col-md-6">
                         <div class="form-group">
                              <label class="label-title">Address <span>*</span></label>
                              <input type="text" class="form-control address" id="address" placeholder="Address">
                         </div>
                      </div>
                      <!-- Condition -->
                      <div class="col-md-6">
                         <div class="form-group">
                              <label class="label-title">Condition <span>*</span></label>
                              <select class="form-control" class="form-control condition" id="condition">
                                    <option>Select Condition</option>
                                    <option>Select Condition</option>
                                    <option>Select Condition</option>
                                    <option>Select Condition</option>
                              </select>
                         </div>
                      </div>
                      <!-- Ad Title -->
                      <div class="col-md-6">
                         <div class="form-group">
                              <label class="label-title">Title for your ad <span>*</span></label>
                              <input type="text" class="form-control ad_title" id="ad_title" placeholder="Type here">
                         </div>
                      </div>
                  </div>

                  <!-- Property Size & Price -->
                  <div class="form-title mb-2 mt-2">
                     <h3>Property Size & Price</h3>
                  </div>
                  <div class="row">
                      <!--  Type A -->
                      <div class="col-md-6">
                           <label class="label-title">Type A</label>
                           <div class="row">
                              <div class="col-6">
                                  <div class="form-group">
                                      <input type="number" class="form-control size" id="size" placeholder="Size in Sft">
                                  </div>
                              </div>
                              <div class="col-6">
                                  <div class="form-group">
                                      <select class="form-control" class="form-control bedroom" id="bedroom">
                                         <option>Bedroom</option>
                                         <option>01</option>
                                         <option>02</option>
                                         <option>03</option>
                                       </select>
                                  </div>
                              </div>
                              <div class="col-6">
                                  <div class="form-group">
                                      <select class="form-control" class="form-control bedroom" id="bedroom">
                                         <option>Bathroom</option>
                                         <option>01</option>
                                         <option>02</option>
                                         <option>03</option>
                                       </select>
                                  </div>
                              </div>
                              <div class="col-6">
                                  <div class="form-group">
                                      <input type="number" class="form-control price" id="price" placeholder="Total Price">
                                  </div>
                              </div>
                          </div>  
                      </div>
                       <!-- Type B -->
                      <div class="col-md-6">
                          <label class="label-title">Type B</label>
                          <div class="row">
                              <div class="col-6">
                                  <div class="form-group">
                                      <input type="number" class="form-control size" id="size" placeholder="Size in Sft">
                                  </div>
                              </div>
                              <div class="col-6">
                                  <div class="form-group">
                                      <input type="number" class="form-control price" id="price" placeholder="Total Price">
                                  </div>
                              </div>
                          </div>
                      </div>
                       <!-- Type c -->
                      <div class="col-md-6">
                          <!-- Type C -->
                          <label class="label-title">Type C</label>
                          <div class="row property-typeC">
                              <div class="col-6 col-sm-3">
                                  <div class="form-group">
                                      <input type="number" class="form-control size" id="size" placeholder="Size in Katha">
                                  </div>
                              </div>
                              <div class="col-6 col-sm-3">
                                  <div class="form-group">
                                      <input type="number" class="form-control price" id="price" placeholder="Total Price">
                                  </div>
                              </div>
                              <div class="col-6 col-sm-3">
                                  <div class="form-group addSize">
                                       <a href="javascript:void(0);">
                                           <i class="fa fa-plus"></i>
                                           Add New Size
                                       </a>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <!-- Property price is -->
                      <div class="col-md-6">
                           <div class="form-group">
                              <label class="label-title">Property price is</label>
                              <input type="radio" checked="" name="priceChek" value="fixed" id="fixed"> 
                              <label for="fixed">Fixed</label>
                              <input type="radio" name="priceChek" value="nagotiable" id="nagotiable"> 
                              <label for="nagotiable">Nagotiable</label>
                          </div>
                      </div>
                  </div>

                  <!--  Additional informamtion -->
                  <div class="form-title mb-2 mt-2">
                     <h3>Additional information</h3>
                  </div>
                  <div class="row">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="label-title">Total Number of Floor</label>
                              <select class="form-control" class="form-control floor" id="floor">
                                    <option>Select Total Floor</option>
                                    <option>1st Floor</option>
                                    <option>1st Floor</option>
                                    <option>1st Floor</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-6">
                         <div class="form-group">
                              <label class="label-title">Floor available</label>
                              <input type="radio" name="floorChek" value="ground" id="ground"> 
                              <label for="ground">Ground Floor</label>
                              <input type="radio" checked="" name="floorChek" value="1arFloor" id="1arFloor"> 
                              <label for="1arFloor">1st Floor</label>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="label-title">Facing</label>
                              <select class="form-control" class="form-control facing" id="facing">
                                    <option>Select Facing</option>
                                    <option>Facing</option>
                                    <option>Facing</option>
                                    <option>Facing</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="label-title">Handover Date</label>
                              <input type="date" class="form-control date" id="date">
                          </div>
                      </div>
                      <div class="col-12">
                          <div class="form-group">
                              <label class="label-title">Descriptions</label>
                              <textarea class="form-control" id="description"></textarea>
                          </div>
                      </div>
                  </div>

                  <div class="row">
                      <div class="col-md-6">
                           <!-- Features -->
                          <div class="form-title mb-2 mt-2">
                             <h3>Features</h3>
                          </div>
                          <div class="form-group">
                              <input type="checkbox" value="parking" id="parking"> 
                              <label for="parking">Parking</label>
                              <input type="checkbox"  checked="" value="gas" id="gas"> 
                              <label for="gas">Gas</label>
                              <input type="checkbox"  checked="" value="water" id="water"> 
                              <label for="water">Water</label>
                              <input type="checkbox"  checked="" value="generator" id="generator"> 
                              <label for="generator">Generator</label>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <!-- Facilities within 1km -->
                          <div class="form-title mb-2 mt-2">
                             <h3>Facilities within 1km</h3>
                          </div>
                          <div class="form-group">
                              <input type="checkbox" value="busStand" id="busStand"> 
                              <label for="busStand">Bus stand</label>
                              <input type="checkbox"  checked="" value="shop" id="shop"> 
                              <label for="shop">Super shop</label>
                              <input type="checkbox"  checked="" value="hospital" id="hospital"> 
                              <label for="hospital">Hospital</label>
                              <input type="checkbox"  checked="" value="school" id="school"> 
                              <label for="school">School</label>
                          </div>
                      </div>

                      <!-- map -->
                      <div class="col-md-6">
                          <div class="form-title mb-2 mt-2">
                             <h3>Property Location on map</h3>
                          </div>
                          <div class="map">
                              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3746841.1426549484!2d88.10013445319406!3d23.49562509219387!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30adaaed80e18ba7%3A0xf2d28e0c4e1fc6b!2sBangladesh!5e0!3m2!1sen!2sbd!4v1622515439402!5m2!1sen!2sbd" style="border:0; width:100%; height: 160px;" allowfullscreen="" loading="lazy"></iframe>
                          </div>
                      </div>

                      <!-- Image & video -->
                      <div class="col-md-6">
                          <div class="form-title mb-2 mt-2">
                             <h3>Image & Video</h3>
                          </div>
                          <div class="form-group">
                               <label class="label-title">Upload Images <span>*</span></label>
                               <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="upload-image">
                                    <label class="custom-file-label">Choose file</label>
                               </div>
                          </div>
                          <div class="form-group">
                              <label class="label-title">Video</label>
                              <input type="url" class="form-control vieo" id="video" placeholder="Paste your Youtube video URL">
                          </div>
                      </div>

                      <!-- Property Owner Details -->
                      <div class="col-md-6">
                          <div class="form-title mb-2 mt-2">
                             <h3>Property Owner Details</h3>
                          </div>
                          <div class="form-group">
                              <label class="label-title">Contact Person</label>
                              <input type="text" class="form-control contactOwner" id="contactOwner">
                          </div>
                          <div class="form-group">
                              <label class="label-title">Mobile</label>
                              <input type="text" class="form-control mobile" id="mobile">
                          </div>
                      </div>

                       <!-- SEO -->
                      <div class="col-md-6">
                          <div class="form-title mb-2 mt-2">
                             <h3>SEO</h3>
                          </div>
                          <div class="form-group">
                              <label class="label-title">Title</label>
                              <input type="text" class="form-control seoTitle" id="seoTitle">
                          </div>
                          <div class="form-group">
                              <label class="label-title">Meta descriptions</label>
                              <textarea class="form-control" id="metaDescr"></textarea>
                          </div>
                      </div>

                       <!-- Site.com -->
                      <div class="col-md-6">
                          <div class="form-title mb-2 mt-2">
                             <h3>Site.com/</h3>
                          </div>
                          <div class="form-group">
                              <input type="url" class="form-control site" id="site">
                          </div>
                      </div>
                  </div>

                  <div class="row">
                       <!-- Listing Type -->
                      <div class="col-md-6">
                          <div class="form-title mb-2 mt-2">
                             <h3>Listing Type</h3>
                          </div>
                          <div class="form-group listingType">
                              <input type="radio" checked="" name="listingType" value="general" id="general"> 
                              <label for="general">General Listing for 30 days</label>
                              <input type="radio" name="listingType" value="features" id="features"> 
                              <label for="features">Feature Listing for 30 days</label>
                              <input type="radio" name="listingType" value="generalAuto" id="generalAuto"> 
                              <label for="generalAuto">General Listing with daily auto update for 30 days</label>
                              <input type="radio" name="listingType" value="featureAuto" id="featureAuto"> 
                              <label for="featureAuto">Feature Listing with daily auto update for 30 days</label>
                          </div>
                      </div>
                    
                      <!-- Publishing Status -->
                      <div class="col-md-6">
                          <div class="form-title mb-2 mt-2">
                             <h3>Publishing Status</h3>
                          </div>
                          <div class="form-group publishingStatus">
                              <input type="radio" checked="" name="publishing" value="pending" id="pending"> 
                              <label for="pending">Pending</label>
                              <input type="radio" name="publishing" value="publish" id="publish"> 
                              <label for="publish">Publish</label>
                              <input type="radio" name="publishing" value="unpublish" id="unpublish"> 
                              <label for="unpublish">Unpublish</label>
                              <input type="radio" name="publishing" value="reject" id="reject"> 
                              <label for="reject">Reject</label>
                              <input type="radio" name="publishing" value="expired" id="expired"> 
                              <label for="expired">Expired</label>
                          </div>
                      </div>
                  </div>

                  <div class="row">
                      <!-- Biling -->
                      <div class="col-12">
                          <div class="form-title mb-2 mt-2">
                             <h3>Billing information</h3>
                          </div>
                          <div class="form-group">
                              <div class="billing-amounot">
                                   <h5>Billin amount: 25 tk</h5>
                              </div>
                              <input type="radio" checked="" name="billing" value="pending" id="pending"> 
                              <label for="pending">Pending</label>
                              <input type="radio" name="billing" value="paid" id="paid"> 
                              <label for="paid">Paid</label>
                          </div>
                      </div>
                      <div class="col-12">
                          <div class="form-group">
                              <div class="custom-control custom-switch">
                                 <input type="checkbox" checked="" class="custom-control-input" id="customSwitch1">
                                 <label class="custom-control-label" for="customSwitch1">Verified BDF</label>
                               </div>
                              <div class="custom-control custom-switch">
                                 <input type="checkbox" class="custom-control-input" id="customSwitch2">
                                 <label class="custom-control-label" for="customSwitch2">Need payment to view CI</label>
                              </div>
                          </div>
                      </div>
                      <div class="col-12 mt-2">
                          <div class="submit-btn">
                              <input type="submit" value="Submit">
                          </div>
                      </div>

                  </div>
            </form>
       </div>   
 </div>
 




                    
                      
 





               
                         
                










@endsection
@push('custom_js')
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script type="text/javascript" src="{{ asset('app-assets/pages/customer.js') }}"></script>
@endpush
