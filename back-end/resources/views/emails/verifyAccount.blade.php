<h3>Hi, {{ $name }}</h3>

<p>Please click the link below to verify your account:</p>

<a href="{{ url('api/verify-email/' . $tokenAcc) }}">Verify Account</a>
