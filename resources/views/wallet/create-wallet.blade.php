@extends('layouts.home')
@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
      <div class="container-xl">
            <div class="row g-2 align-items-center">
                  <div class="col">
                        <!-- Page pre-title -->
                        <div class="page-pretitle">
                              Create Wallet
                        </div>
                        <h2 class="page-title">
                              <span class=" d-none  d-md-block">Wallet</span>
                        </h2>
                  </div>
                  <!-- Page title actions -->
                  <div class="col-auto ms-auto d-print-none">
                    
                        <div class="btn-list">
                              <span class="d-block ">
                                    <a href="#" class="btn d-none ">
                                    </a>
                              </span>
                              <a href="{{ url('wallet')  }}" class="btn btn-danger d-none d-sm-inline-block">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye"
                                          width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5"
                                          stroke="currentColor" fill="none" stroke-linecap="round"
                                          stroke-linejoin="round">
                                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                          <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                          <path
                                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                    </svg>

                                    View Wallet
                              </a>
                              <a href="{{ url('wallet')  }}" class="btn btn-danger d-sm-none btn-icon">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye"
                                          width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5"
                                          stroke="currentColor" fill="none" stroke-linecap="round"
                                          stroke-linejoin="round">
                                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                          <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                          <path
                                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                    </svg>

                              </a>
                        </div>
                     
                  </div>
            </div>
      </div>
</div>

<!-- Page body -->
<div class="page-body">
      <div class="container-xl">
            <div class="row row-deck row-cards">
                  <!-- Alert start --->
                  <div class="container-xl">
                        <div class="row ">
                              <div class="col-12">
                                    <p></p>
                                    @if(session('sms-error'))
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                          <div class="d-flex">
                                                <div>
                                                      <!-- Download SVG icon from http://tabler-icons.io/i/alert-circle -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon"
                                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                            <path d="M12 8v4" />
                                                            <path d="M12 16h.01" />
                                                      </svg>

                                                </div>
                                                <div> {{ session('sms-error') }}</div>
                                          </div>
                                          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                                    </div>
                                    @endif

                                    @if(session('sms'))
                                    <div class="alert alert-important alert-info alert-dismissible" role="alert">
                                          <div class="d-flex">
                                                <div>
                                                      <!-- Download SVG icon from http://tabler-icons.io/i/alert-circle -->
                                                      <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon"
                                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                            <path d="M12 8v4" />
                                                            <path d="M12 16h.01" />
                                                      </svg>

                                                </div>
                                                <div> {{ session('sms') }}</div>
                                          </div>
                                          <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                                    </div>
                                    @endif
                              </div>
                        </div>
                  </div>
        <!-- Alert stop --->
                  <h4>Kindly complete your KYC form below:</h4>
                  <span>All field marke <span class="text-danger">*</span> are required</span>
                  <div class="col-12">

                        <div class="card">
                              <div class="card-body">
                                    <div class="row">
                                          <div class="col-md">
                                                <div class="form-label required">Surname
                                                </div>
                                                <input type="text" class="form-control" name="surname">
                                          </div>
                                          <div class="col-md">
                                                <div class="form-label required">First Name</div>
                                                <input type="text" class="form-control" name="firstname">
                                          </div>
                                          <p></p>
                                    </div>

                                    <div class="row">
                                          <div class="col-md">
                                                <div class="form-label required">Phone
                                                </div>
                                                <input type="text" class="form-control" name="phone">
                                          </div>
                                          <div class="col-md">
                                                <div class="form-label required">Gender</div>
                                                <select name="gender" id="" class="form-control">
                                                      <option value="">Choose</option>
                                                      <option value="male">Male</option>
                                                      <option value="female">Female</option>
                                                </select>
                                          </div>
                                          <p></p>
                                    </div>


                                    <div class="row">
                                          <div class="col-md">
                                                <div class="form-label required">Date Of Birth
                                                </div>
                                                <div class="input-icon mb-2">
                                                      <span class="input-icon-addon">
                                                            <!-- Download SVG icon from http://tabler-icons.io/i/calendar -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon"
                                                                  width="24" height="24" viewBox="0 0 24 24"
                                                                  stroke-width="2" stroke="currentColor" fill="none"
                                                                  stroke-linecap="round" stroke-linejoin="round">
                                                                  <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                  </path>
                                                                  <path
                                                                        d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z">
                                                                  </path>
                                                                  <path d="M16 3v4"></path>
                                                                  <path d="M8 3v4"></path>
                                                                  <path d="M4 11h16"></path>
                                                                  <path d="M11 15h1"></path>
                                                                  <path d="M12 15v3"></path>
                                                            </svg>
                                                      </span>
                                                      <input class="form-control " placeholder="Select a date"
                                                            id="datepicker-icon" name="payOutDate" value="">

                                                </div>
                                          </div>
                                          <div class="col-md">
                                                <div class="form-label required">BVN</div>
                                                <input type="text" class="form-control" name="bvn"
                                                      placeholder="Valid bvn number only " value="" id="bvn"
                                                      maxlength="11" onkeyup="check_account_number()">
                                                <!---bvn number validation error --->
                                                <span id="check_bvn"></span>
                                                <span class="text-danger" id="bvnError"></span>
                                                <div class="progress" id="show-progress" style="display:none;">
                                                      <div class="progress-bar progress-bar-indeterminate bg-green">
                                                      </div>
                                                </div>
                                                <!--- end validation error --->
                                          </div>

                                    </div>
                                    <!--- row --->

                                    <!-- send button here -->
                                    <div class="card-footer bg-transparent mt-auto">
                                          <div class="btn-list justify-content-end">
                                                <button type="submit" name="submit" id="submit" style="display: none"
                                                      class="btn btn-danger ms-auto">
                                                      <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="icon icon-tabler icon-tabler-device-floppy"
                                                            width="24" height="24" viewBox="0 0 24 24"
                                                            stroke-width="1.5" stroke="currentColor" fill="none"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                  d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                                                            <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                            <path d="M14 4l0 4l-6 0l0 -4" />
                                                      </svg>
                                                      Next
                                                </button>

                                                <button type="button" name="submit" id="hideSubmitButton" disabled=""
                                                      class="btn btn-danger ms-auto">
                                                      <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="icon icon-tabler icon-tabler-device-floppy"
                                                            width="24" height="24" viewBox="0 0 24 24"
                                                            stroke-width="1.5" stroke="currentColor" fill="none"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                  d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                                                            <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                            <path d="M14 4l0 4l-6 0l0 -4" />
                                                      </svg>
                                                      Next
                                                </button>
                                          </div>
                                    </div>

                              </div>
                        </div><!-- Card-->

                  </div>
            </div>
      </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script>
function check_account_number() {
      let account = document.getElementById('bvn').value;
      let bvn = document.getElementById('bvn').value;
      document.getElementById('show-progress').style.display = 'none';
      document.getElementById('hideSubmitButton').style.display = '';
      document.getElementById('submit').style.display = 'none';


      if (account.length < 11) {
            document.getElementById('hideSubmitButton').style.display = '';
            document.getElementById('submit').style.display = 'none';
            document.getElementById('show-progress').style.display = 'none';
            document.getElementById('check_bvn').style.color = 'red';
            document.getElementById('check_bvn').innerHTML = '☒ BVN number must be 11 digits ';

      } else {
            document.getElementById('hideSubmitButton').style.display = '';
            document.getElementById('submit').style.display = 'none';
            document.getElementById('check_bvn').innerHTML = ' ';
            document.getElementById('show-progress').style.display = '';


            // Make the fetch request with the variables
            //Test secret key: sk_test_b665e0b51fe5f6df4ea0f29a56d8d84b74eca251
            //live secret key : sk_live_883a3c48aa5da65d6793526046220d82ea0f1c16
            const fetchPromise =
                  fetch("https://api.paystack.co/bank/resolve_bvn/"+bvn, {
                        headers: {
                              Authorization: 'Bearer sk_live_883a3c48aa5da65d6793526046220d82ea0f1c16'
                        }
                  });
            // Timeout after 120 seconds//  2 min
            const timeoutId = setTimeout(() => {
                  controller.abort(); // Abort the fetch request
                  console.log('Timed out');
            }, 120000);

            // Handle the fetch request
            fetchPromise
                  .then(response => {
                        // Check if the request was successful
                        if (!response.ok) {
                              throw new Error('Slow network!');
                        }
                        // Parse the response as JSON
                        return response.json();
                  })
                  .then(data => {
                        // Handle the JSON data
                        console.log(data.data.account_name);
                        document.getElementById('hideSubmitButton').style.display = 'none';
                        document.getElementById('submit').style.display = '';
                        document.getElementById('show-progress').style.display = 'none';
                        document.getElementById('check_bvn').style.color = 'green';
                        document.getElementById('check_bvn').innerHTML = '☒ validated ';
                  })
                  .catch(error => {
                        // Handle any errors that occurred during the fetch
                        console.error('Fetch error:', error);
                        document.getElementById('accountError').innerHTML = error;
                  })
                  .finally(() => {
                        clearTimeout(timeoutId); // Clear the timeout
                  });
      }



}
</script>
@endsection