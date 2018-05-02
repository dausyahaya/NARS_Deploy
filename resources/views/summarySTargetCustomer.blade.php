@extends('layouts.app1')
@section('head')
<style>
.sumtable{
  background-color:#fff;
  font-size: 12px;
  margin-left:15%;
  margin-right:15%;
  color:#fff;
  width:100%;
  height:300px;
}
table{
  background:#fff;
}
th, td {
    text-align: left;
    padding: 8px;
    background:#fff;
}
tr:nth-child(even){background-color: #f2f2f2}
</style>
@endsection
@section('content')

<script>
$(document).ready(function(){
  $('#sales-per-outlet').DataTable();
});
</script>

<section id="admin">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 text-center">
        <h2 class="section-heading text-uppercase">SALES SUMMARY</h2>
        <h3 class="section-subheading text-muted">Lorem ipsum dolor sit amet consectetur.</h3>
      </div>
    </div>
    <div class="row">

      <div class="col-lg-12">
        <form id="promoForm" name="sentMessage" novalidate>
          <div class="row">
            <div class="wrapper" style="background:#fff;margin: 0 auto;">
                <center><h3>Item Sales Per Outlet ~ Quantity & Sales Figure</h3></center>
                <table class="table table-striped sumtable" id='sales-per-outlet'>
                  <thead>
                    <tr>
                      <th>Num</th>
                      <th>Customer Name</th>
                      <th>INVC NO</th>
                      <th>Rolling Month</th>
                      <th>Qty Sold</th>
                      <th>Sales</th>
                      <th>Sales Before GST</th>
                      <th>Disc%</th>
                      <th>Price</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($sales7 as $index => $row)
                    <tr>
                      <td>{{$index +1}}</td>
                      <td>{{$row->Customer_Name}}</td>
                      <td>{{$row->INVC_NO}}</td>
                      <td>{{$row->Rolling_Month}}</td>
                      <td>{{$row->Qty_Sold}}</td>
                      <td>{{$row->Sales}}</td>
                      <td>{{$row->before_gst}}</td>
                      <td>{{$row->Disc}}</td>
                      <td>{{$row->Price}}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
            </div>


          </div>
        </form>
      </div>
    </br></br>
    &nbsp;


      <div class="col-lg-12 text-center">
        <div id="success"></div>
        <a href='{{ URL::to("/catsalessummary")}}'><button id="backbutton" class="btn btn-primary btn-xl text-uppercase" type="button">BACK</button></a>
      </div>
    </div>
  </div>
</section>

@endsection
