import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:8000"
USERNAME = "tester"
PASSWORD = "Passw0rd!"
TIMEOUT = 30

def test_post_logout_session_termination():
    # Step 1: Login to get a session/token
    login_url = f"{BASE_URL}/login"
    login_payload = {"username": USERNAME, "password": PASSWORD}
    headers = {"Content-Type": "application/json"}

    try:
        login_response = requests.post(login_url, json=login_payload, headers=headers, timeout=TIMEOUT)
        assert login_response.status_code == 200, f"Login failed with status code {login_response.status_code}"
        # Assuming the API returns a token or sets a session cookie
        if "application/json" in login_response.headers.get("Content-Type", ""):
            login_data = login_response.json()
            # Check for token or session key
            token = login_data.get("token")
            cookies = None
            if token:
                auth_headers = {"Authorization": f"Bearer {token}"}
            else:
                # fallback if no token, try cookies from response (session)
                auth_headers = {}
                cookies = login_response.cookies
        else:
            # fallback if no JSON response
            auth_headers = {}
            cookies = login_response.cookies

        # Step 2: Verify server is responding with 200 on a protected sales endpoint
        sales_url = f"{BASE_URL}/ventas"
        sales_resp = requests.get(sales_url, headers=auth_headers, cookies=cookies, timeout=TIMEOUT)
        assert sales_resp.status_code == 200, f"Sales endpoint not accessible after login, status code: {sales_resp.status_code}"

        # Step 3: Call logout endpoint
        logout_url = f"{BASE_URL}/logout"
        logout_resp = requests.post(logout_url, headers=auth_headers, cookies=cookies, timeout=TIMEOUT)
        assert logout_resp.status_code == 200, f"Logout failed, status code: {logout_resp.status_code}"

        # Step 4: Attempt access to sales endpoint again to ensure session is terminated
        post_logout_sales_resp = requests.get(sales_url, headers=auth_headers, cookies=cookies, timeout=TIMEOUT)
        # Expect unauthorized access after logout
        assert post_logout_sales_resp.status_code in (401, 403), (
            f"Access to sales endpoint after logout was not revoked, status code: {post_logout_sales_resp.status_code}"
        )
    except requests.RequestException as e:
        assert False, f"Request failed: {e}"

test_post_logout_session_termination()
