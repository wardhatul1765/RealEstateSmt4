<h2>Register</h2>
<form method="POST" action="/register">
    @csrf
    <input name="name" placeholder="Name" required>
    <input name="email" placeholder="Email" type="email" required>
    <input name="password" placeholder="Password" type="password" required>
    <input name="password_confirmation" placeholder="Confirm Password" type="password" required>
    <button type="submit">Register</button>
</form>
<a href="/login">Already have an account? Login</a>
