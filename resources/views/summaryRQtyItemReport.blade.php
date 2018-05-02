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
  $('#item-report').DataTable();
});
</script>

<section id="admin">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 text-center">
        <h2 class="section-heading text-uppercase">REDEMPTION SUMMARY</h2>
        <h3 class="section-subheading text-muted">Lorem ipsum dolor sit amet consectetur.</h3>
      </div>
    </div>
    <div class="row">

      <!--3 - Redemption Item Report (quantity for each item) -->

      <div class="col-lg-12">
        <form id="promoForm" name="sentMessage" novalidate>
          <div class="row">
            <div class="wrapper" style="background:#fff;margin: 0 auto;">
                <center><h3>Redemption Item Report (quantity for each item)</h3></center>
                <table class="table table-striped sumtable" id='item-report'>
                  <thead>
                    <tr>
                      <th>Num</th>
                      <th>DCS_Code</th>
                      <th>ALU</th>
                      <th>Item's Name</th>
                      <th>#</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($redemption3 as $index => $row)
                    <tr>
                      <td>{{$index +1}}</td>
                      <td>{{$row->DCS_Code}}</td>
                      <td>{{$row->ALU}}</td>
                      <td>{{$row->Name}}</td>
                      <td>{{$row->Quantity}}</td>
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
        <a href='{{ URL::to("/catredeemsummary")}}'><button id="backbutton" class="btn btn-primary btn-xl text-uppercase" type="button">BACK</button></a>
      </div>
    </div>
  </div>
</section>

@endsection
