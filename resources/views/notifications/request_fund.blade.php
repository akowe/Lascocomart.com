<p>New Request for fund: </p>
<p>{{ $data['cooperative_name'] }} with below details, is requesting fund of  {{ number_format($data['amount'])}} <br>
<strong>Cooperative Code:</strong> {{ $data['cooperative_code'] }}<br>
<strong>Cooperative Email:</strong> {{ $data['email'] }}<br>
<strong>Amount Requested:</strong>  {{ number_format($data['amount'])}}<br>
</p>

<p>Login <a href="{{ route('superadmin') }}">here</a> to see approve fund.</p>
<p>Thank you for using LascocoMart.</p>

