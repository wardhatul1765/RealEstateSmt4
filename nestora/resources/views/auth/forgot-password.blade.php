<h2>Forgot Password</h2>
<form method="POST" action="/forgot-password">
    @csrf
    <input name="email" placeholder="Enter your email" type="email" required>
    <button type="submit">Send Password Reset Link</button>
</form>
<a href="/login">Back to login</a>
