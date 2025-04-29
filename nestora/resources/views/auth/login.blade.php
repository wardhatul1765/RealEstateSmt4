<h2>Login</h2>
<form method="POST" action="/login">
    @csrf
    <input name="email" placeholder="Email" type="email" required>
    <input name="password" placeholder="Password" type="password" required>
    <button type="submit">Login</button>
</form>
<a href="/register">Register</a>
<a href="/forgot-password">Forgot Password?</a>
