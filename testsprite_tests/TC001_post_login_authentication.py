import requests

BASE_URL = "http://localhost:8000"
LOGIN_ENDPOINT = "/login"
TIMEOUT = 30

def test_post_login_authentication():
    # Valid credentials
    valid_credentials = {
        "username": "tester",
        "password": "Passw0rd!"
    }

    # Invalid credentials (wrong password)
    invalid_credentials = {
        "username": "tester",
        "password": "WrongPassword"
    }

    headers = {
        "Content-Type": "application/json"
    }

    # Test server is responding with status 200 on GET /
    try:
        resp_root = requests.get(BASE_URL, timeout=TIMEOUT)
        assert resp_root.status_code == 200, f"Expected 200 OK from server root, got {resp_root.status_code}"
    except requests.RequestException as e:
        assert False, f"Server not responding at root: {e}"

    # 1. Test login with valid credentials
    try:
        resp_valid = requests.post(
            BASE_URL + LOGIN_ENDPOINT,
            json=valid_credentials,
            headers=headers,
            timeout=TIMEOUT
        )
    except requests.RequestException as e:
        assert False, f"Login request with valid credentials failed: {e}"

    # Validate response for valid login
    assert resp_valid.status_code == 200, f"Valid login expected status 200, got {resp_valid.status_code}"

    # Verify session is set (expect Set-Cookie header)
    cookie_header = resp_valid.headers.get('Set-Cookie')
    assert cookie_header is not None and cookie_header != '', "Valid login response missing Set-Cookie header for session management"

    # 2. Test login with invalid credentials
    try:
        resp_invalid = requests.post(
            BASE_URL + LOGIN_ENDPOINT,
            json=invalid_credentials,
            headers=headers,
            timeout=TIMEOUT
        )
    except requests.RequestException as e:
        assert False, f"Login request with invalid credentials failed: {e}"

    # Validate response for invalid login, commonly 401 or 403
    assert resp_invalid.status_code in (401, 403), f"Invalid login expected status 401 or 403, got {resp_invalid.status_code}"

    # Verify no Set-Cookie on invalid login
    invalid_cookie = resp_invalid.headers.get('Set-Cookie')
    assert invalid_cookie is None or invalid_cookie == '', "Invalid login response should not contain Set-Cookie header"

    # Optional: If session cookie returned, test session management by accessing an authenticated endpoint
    if cookie_header:
        headers_auth = {
            "Cookie": cookie_header,
            "Content-Type": "application/json"
        }
        # Example authenticated endpoint to check session validity: GET /ventas (sales - requires auth)
        try:
            resp_auth_check = requests.get(
                BASE_URL + "/ventas",
                headers=headers_auth,
                timeout=TIMEOUT
            )
            assert resp_auth_check.status_code == 200, f"Authenticated access failed with status code {resp_auth_check.status_code}"
        except requests.RequestException as e:
            assert False, f"Authenticated request failed: {e}"

test_post_login_authentication()
