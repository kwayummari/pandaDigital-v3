<?php

/**
 * Profile Completion Modal Component
 * Include this file in pages where profile completion is required
 */

// Initialize User model if not already done
if (!isset($userModel)) {
    require_once __DIR__ . '/../models/User.php';
    $userModel = new User($pdo);
}

// Get current user ID from session
$currentUserId = $_SESSION['userId'] ?? null;

if ($currentUserId) {
    $profileStatus = $userModel->getProfileCompletionStatus($currentUserId);
    $fieldLabels = $userModel->getFieldLabels();
    $regions = $userModel->getRegions();
    $genderOptions = $userModel->getGenderOptions();
}
?>

<!-- Profile Completion Modal -->
<div class="modal fade" id="profileCompletionModal" tabindex="-1" aria-labelledby="profileCompletionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="profileCompletionModalLabel">
                    <i class="fas fa-user-edit me-2"></i>
                    Kamilisha Wasifu Wako
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold">Ukomo wa Wasifu:</span>
                        <span class="badge bg-primary"><?php echo $profileStatus['percentage'] ?? 0; ?>%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" role="progressbar"
                            style="width: <?php echo $profileStatus['percentage'] ?? 0; ?>%"
                            aria-valuenow="<?php echo $profileStatus['percentage'] ?? 0; ?>"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted">
                        <?php echo $profileStatus['completed'] ?? 0; ?> kati ya <?php echo $profileStatus['total'] ?? 0; ?> sehemu zimekamilika
                    </small>
                </div>

                <!-- Profile Form -->
                <form id="profileCompletionForm" method="POST" action="update-profile.php">
                    <input type="hidden" name="user_id" value="<?php echo $currentUserId; ?>">

                    <div class="row">
                        <!-- First Name -->
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">
                                <?php echo $fieldLabels['first_name'] ?? 'Jina la Kwanza'; ?>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                value="<?php echo htmlspecialchars($_SESSION['userFirstName'] ?? ''); ?>" required>
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">
                                <?php echo $fieldLabels['last_name'] ?? 'Jina la Mwisho'; ?>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                value="<?php echo htmlspecialchars($_SESSION['userLastName'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Phone -->
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">
                                <?php echo $fieldLabels['phone'] ?? 'Nambari ya Simu'; ?>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                value="<?php echo htmlspecialchars($_SESSION['userPhone'] ?? ''); ?>" required>
                        </div>

                        <!-- Region -->
                        <div class="col-md-6 mb-3">
                            <label for="region" class="form-label">
                                <?php echo $fieldLabels['region'] ?? 'Mkoa'; ?>
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="region" name="region" required>
                                <option value="">Chagua Mkoa</option>
                                <?php foreach ($regions as $region): ?>
                                    <option value="<?php echo htmlspecialchars($region); ?>"
                                        <?php echo (($_SESSION['userRegion'] ?? '') === $region) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($region); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Business -->
                        <div class="col-md-6 mb-3">
                            <label for="business" class="form-label">
                                <?php echo $fieldLabels['business'] ?? 'Biashara'; ?>
                            </label>
                            <input type="text" class="form-control" id="business" name="business"
                                value="<?php echo htmlspecialchars($_SESSION['userBusiness'] ?? ''); ?>"
                                placeholder="Jina la biashara yako (si lazima)">
                        </div>

                        <!-- Gender -->
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">
                                <?php echo $fieldLabels['gender'] ?? 'Jinsia'; ?>
                            </label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Chagua Jinsia</option>
                                <?php foreach ($genderOptions as $value => $label): ?>
                                    <option value="<?php echo htmlspecialchars($value); ?>"
                                        <?php echo (($_SESSION['userGender'] ?? '') === $value) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Date of Birth -->
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label">
                                <?php echo $fieldLabels['date_of_birth'] ?? 'Tarehe ya Kuzaliwa'; ?>
                            </label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                value="<?php echo htmlspecialchars($_SESSION['userDateOfBirth'] ?? ''); ?>">
                        </div>

                        <!-- Bio -->
                        <div class="col-md-6 mb-3">
                            <label for="bio" class="form-label">
                                <?php echo $fieldLabels['bio'] ?? 'Maelezo Binafsi'; ?>
                            </label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"
                                placeholder="Maelezo mafupi kuhusu wewe..."><?php echo htmlspecialchars($_SESSION['userBio'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Action Required Message -->
                    <div class="alert alert-info" id="actionRequiredMessage" style="display: none;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Ujumbe:</strong> <span id="actionMessageText"></span>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Funga
                </button>
                <button type="submit" form="profileCompletionForm" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Hifadhi Wasifu
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Profile Completion Modal -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileCompletionForm = document.getElementById('profileCompletionForm');
        const actionRequiredMessage = document.getElementById('actionRequiredMessage');
        const actionMessageText = document.getElementById('actionMessageText');

        // Handle form submission
        if (profileCompletionForm) {
            profileCompletionForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Inahifadhi...';
                submitBtn.disabled = true;

                // Submit form via AJAX
                fetch('update-profile.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            showAlert('Wasifu wako umekamilishwa kwa mafanikio!', 'success');

                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('profileCompletionModal'));
                            if (modal) {
                                modal.hide();
                            }

                            // Reload page to reflect changes
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showAlert(data.message || 'Kuna tatizo. Tafadhali jaribu tena.', 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('Kuna tatizo. Tafadhali jaribu tena.', 'danger');
                    })
                    .finally(() => {
                        // Restore button state
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
            });
        }

        // Function to show profile completion modal with specific action message
        window.showProfileCompletionModal = function(action, actionName) {
            const modal = new bootstrap.Modal(document.getElementById('profileCompletionModal'));

            // Set action-specific message
            const actionMessages = {
                'download_certificate': 'Unahitaji kukamilisha wasifu wako ili kupakua cheti.',
                'contact_expert': 'Unahitaji kukamilisha wasifu wako ili kuwasiliana na mtaalamu.',
                'sell_product': 'Unahitaji kukamilisha wasifu wako ili kuuza bidhaa.',
                'buy_product': 'Unahitaji kukamilisha wasifu wako ili kununua bidhaa.',
                'study_course': 'Unahitaji kukamilisha wasifu wako ili kusoma kozi.'
            };

            if (actionMessages[action]) {
                actionMessageText.textContent = actionMessages[action];
                actionRequiredMessage.style.display = 'block';
            } else {
                actionRequiredMessage.style.display = 'none';
            }

            modal.show();
        };

        // Function to check if profile completion is required for an action
        window.checkProfileCompletion = function(action, actionName) {
            // This will be implemented to check with the server
            // For now, we'll show the modal directly
            showProfileCompletionModal(action, actionName);
            return false; // Prevent the original action
        };
    });

    // Function to show alerts (if not already defined)
    function showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('alertContainer') || createAlertContainer();

        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

        alertContainer.appendChild(alert);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    function createAlertContainer() {
        const container = document.createElement('div');
        container.id = 'alertContainer';
        container.className = 'position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }
</script>