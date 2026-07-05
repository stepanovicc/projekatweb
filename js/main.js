"use strict";

const emailInput = document.getElementById('email');
const emailFeedback = document.getElementById('email-feedback');

if (emailInput && emailFeedback) {
    let emailTimer = null;

    emailInput.addEventListener('input', () => {
        clearTimeout(emailTimer);
        const email = emailInput.value.trim();
        emailFeedback.textContent = '';
        emailFeedback.className = '';

        if (!isValidEmail(email)) return;

        emailTimer = setTimeout(() => {
            checkEmailAvailability(email);
        }, 600);
    });
}

function checkEmailAvailability(email) {
    fetch('php/ajax/check_email.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email })
    })
    .then(res => res.json())
    .then(data => {
        if (!emailFeedback) return;
        if (data.available) {
            emailFeedback.textContent = '✓ Email is available';
            emailFeedback.className = 'text-success small';
        } else {
            emailFeedback.textContent = '✗ Email already registered';
            emailFeedback.className = 'text-danger small';
        }
    })
    .catch(() => {});
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

const roleSelect = document.getElementById('role');
const companyFields = document.getElementById('company-fields');

if (roleSelect && companyFields) {
    roleSelect.addEventListener('change', () => {
        if (roleSelect.value === 'company') {
            companyFields.classList.remove('d-none');
            companyFields.querySelectorAll('input, textarea').forEach(el => {
                el.required = true;
            });
        } else {
            companyFields.classList.add('d-none');
            companyFields.querySelectorAll('input, textarea').forEach(el => {
                el.required = false;
                el.value = '';
            });
        }
    });
}

const passwordInput = document.getElementById('password');
const strengthBar = document.getElementById('password-strength');

if (passwordInput && strengthBar) {
    passwordInput.addEventListener('input', () => {
        const val = passwordInput.value;
        const score = getPasswordScore(val);
        const levels = ['', 'bg-danger', 'bg-warning', 'bg-info', 'bg-success'];
        const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
        strengthBar.style.width = (score * 25) + '%';
        strengthBar.className = 'progress-bar ' + (levels[score] || '');
        strengthBar.textContent = labels[score] || '';
    });
}

function getPasswordScore(pwd) {
    let score = 0;
    if (pwd.length >= 8)  score++;
    if (/[A-Z]/.test(pwd)) score++;
    if (/[0-9]/.test(pwd)) score++;
    if (/[^A-Za-z0-9]/.test(pwd)) score++;
    return score;
}

const forms = document.querySelectorAll('.needs-validation');

forms.forEach(form => {
    form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});

const searchForm = document.getElementById('job-search-form');
const jobResults = document.getElementById('job-results');

if (searchForm && jobResults) {
    searchForm.addEventListener('submit', event => {
        event.preventDefault();
        fetchJobs();
    });

    searchForm.querySelectorAll('select').forEach(el => {
        el.addEventListener('change', fetchJobs);
    });
}

function fetchJobs() {
    if (!searchForm || !jobResults) return;

    const formData = new FormData(searchForm);
    const params = new URLSearchParams(formData).toString();

    jobResults.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-accent" role="status"></div></div>';

    fetch('php/ajax/get_jobs.php?' + params)
    .then(res => res.json())
    .then(data => {
        if (data.length === 0) {
            jobResults.innerHTML = '<div class="col-12 text-center py-5 text-muted"><i class="bi bi-search fs-1 d-block mb-3"></i>No jobs found. Try adjusting your filters.</div>';
            return;
        }
        jobResults.innerHTML = data.map(job => buildJobCard(job)).join('');
    })
    .catch(() => {
        jobResults.innerHTML = '<div class="col-12 text-center text-danger py-4">Failed to load jobs. Please try again.</div>';
    });
}

function buildJobCard(job) {
    const isExpired = new Date(job.deadline) < new Date();
    const expiredLabel = isExpired ? '<span class="expired-label">Expired</span>' : '';

    return `
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="job-card h-100 ${isExpired ? 'expired' : ''}">
                ${expiredLabel}
                <span class="badge-category mb-2 d-inline-block">${escapeHtml(job.category_name)}</span>
                <h5 class="mb-1">${escapeHtml(job.title)}</h5>
                <div class="company-name mb-2">${escapeHtml(job.company_name)}</div>
                <div class="job-meta mb-3">
                    <i class="bi bi-geo-alt me-1"></i>${escapeHtml(job.location)}
                    &nbsp;·&nbsp;
                    <i class="bi bi-calendar3 me-1"></i>Deadline: ${escapeHtml(job.deadline)}
                </div>
                <a href="job.php?id=${encodeURIComponent(job.id)}" class="btn btn-sm btn-outline-secondary w-100">View Details</a>
            </div>
        </div>
    `;
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = String(str);
    return div.innerHTML;
}

const applyBtn = document.getElementById('apply-btn');
const applyFeedback = document.getElementById('apply-feedback');

if (applyBtn && applyFeedback) {
    applyBtn.addEventListener('click', () => {
        applyToJob(applyBtn.dataset.jobId, applyBtn.dataset.csrfToken);
    });
}

function applyToJob(jobId, csrfToken) {
    applyBtn.disabled = true;

    fetch('php/ajax/apply_job.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ job_id: jobId, csrf_token: csrfToken })
    })
    .then(res => res.json())
    .then(data => {
        applyFeedback.textContent = data.message;
        applyFeedback.className = 'mt-2 small ' + (data.success ? 'text-success' : 'text-danger');

        if (data.success || data.alreadyApplied) {
            const label = document.getElementById('apply-btn-label');
            if (label) label.textContent = 'Already Applied';
        } else {
            applyBtn.disabled = false;
        }
    })
    .catch(() => {
        applyFeedback.textContent = 'Failed to submit application. Please try again.';
        applyFeedback.className = 'mt-2 small text-danger';
        applyBtn.disabled = false;
    });
}

document.querySelectorAll('.alert-dismissible').forEach(alert => {
    setTimeout(() => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
        bsAlert.close();
    }, 5000);
});
