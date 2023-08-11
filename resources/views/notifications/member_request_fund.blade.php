<p>New Request for fund: </p>
<p>Hello {{ $data['cooperative_name'] }}, your member with below details, is requesting fund of  {{ number_format($data['amount'])}} <br>
<strong>Full name:</strong> {{ $data['first_name'] }} {{ $data['last_name'] }}<br>
<strong>Cooperative Email:</strong> {{ $data['email'] }}<br>
<strong>Amount Requested:</strong>  {{ number_format($data['amount'])}}<br>
</p>

<p>Login <a href="{{ route('cooperative') }}">here</a> to see approve fund.</p>
<p>Thank you for using LascocoMart.</p>

