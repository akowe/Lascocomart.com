@extends('layouts.home')

@extends('layouts.sidebar')
@section('content')
<div class="adminx-content">
      <div class="adminx-main-content">
            <div class="container-fluid">
                  <!-- BreadCrumb -->
                  <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb adminx-page-breadcrumb">
                              <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                              <li class="breadcrumb-item active" aria-current="page">Cooperative</li>
                        </ol>
                  </nav>

                  <div class="pb-3">
                        <h1>Bank Transfer</h1>

                        <h5 class="navbar bg-dark text-white" style="padding-left: 10px;">
                              Your LascocoMart User ID is: &nbsp; {{Auth::user()->code}}. <br>
                        </h5>
                  </div>
                  <p class=""><strong>Pay with Transfer using your nternet banking.</strong> </p>
                  <p class="text-danger">On pop-up click "Transfer"</p>
                  <p class="text-danger"><span class="text-dark"> <strong>Next:</strong> </span> Go to you internet banking, transfer to the unique account number on your screen. </p>
                  <p class="text-danger"><span class="text-dark"> <strong>Next:</strong></span>  Do not save the account number</p>
                  <p class="text-danger"><span class="text-dark"><strong>Next:</strong></span> Click "i have  sent the money". wait for it...</p>
            </div>
            <div class="container">
                  <div class="row">
                        <div class="col-md-6">

                              <form id="paymentForm" method="GET" name="submit">
                                    @csrf
                                    <script src="https://js.paystack.co/v1/inline.js"></script>
                                    <div class="form-group">
                                          <input type="hidden"   class="form-control"  id="email" name="email" readonly
                                                value="{{Auth::user()->email}}" />
                                    </div>
                                    <div class="form-group">
                                          <label for="amount">Total Amount</label>
                                          <input type="text"  class="form-control"  id="amount" name="amount" readonly
                                                value="{{$all_orders->sum('grandtotal')}}" />

                                    </div>
                                    <div class="form-group">
                                          <input type="hidden" class="form-control" id="order_id" name="id[][id]"
                                                value="{{ $orders->pluck('id') }}" />

                                    </div>
                                    <div class="form-submit">
                                          <button type="button" class=" btn btn-success" name="submit" onclick=" payWithPaystack() "> Transfer
                                                Now </button>
                                    </div>
                              </form>
                        </div>
                  </div>
            </div>

            <script>
            var paymentForm = document.getElementById('paymentForm');
            paymentForm.addEventListener('submit', payWithPaystack, false);

            function payWithPaystack() {
                  var handler = PaystackPop.setup({
                       // pk_live_483aa57c04940d7f7565b235ab06f9621a15ef25
                       //pk_test_6ce6e9d31412d8fd2574af630c65d0e9aa78c5c7
                        key: 'pk_test_6ce6e9d31412d8fd2574af630c65d0e9aa78c5c7',
                        email: document.getElementById("email").value,
                        amount: document.getElementById("amount").value *
                              100, // the amount value is multiplied by 100 to convert to the lowest currency unit
                        currency: 'NGN', // Use GHS for Ghana Cedis or USD for US Dollars

                        reference: '' + Math.floor((Math.random() * 1000000000) +
                              1
                        ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                        metadata: {
                              custom_fields: [{
                                    order_id: document.getElementById("order_id").value
                              }]
                        },


                        callback: function(response) {
                              var email = document.getElementById("email").value;
                              var amount = document.getElementById("amount").value;
                              var order_id = document.getElementById("order_id").value;

                              var paystack_reference = response.reference;
                              var reference = paystack_reference;

                              // var success ='ref='+paystack_reference + '&email=' + email + '&amount=' +
                              // amount + '&order_id=' + order_id;
                              var contact = JSON.parse(order_id);

                              var ref = paystack_reference;
                              var order_id = JSON.parse(order_id);
                              var order_amount = +amount;

                              // let message = 'Payment complete! Reference number: ' + response.reference;
                              // alert(order_id);

                              var url = "{{ URL('payment-bank-tranfer/') }}" + "/" + reference + "/" +
                                    order_id + "/" + order_amount;
                              location.href = url;

                              //or

                              // window.location = "<?php echo url('payment-bank-tranfer');?>/success=" +
                              //       paystack_reference + '&email=' + email + '&amount=' + amount +
                              //       '&order_id=' + order_id;

                        },


                        onClose: function() {
                              alert('Transaction was not completed, window closed.');
                        }
                  });
                  handler.openIframe();
            }
            </script>

      </div>
</div>

@endsection