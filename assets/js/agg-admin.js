(function($) {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        const buttonGroups = document.querySelectorAll('.agg-button-group');

        function showUpgradeModal() {
            const modalHtml = `
                <div class="agg-upgrade-modal">
                    <div class="agg-upgrade-content">
                        <h3>${aggL10n.upgradeTitle}</h3>
                        <p>${aggL10n.upgradeMessage}</p>
                        <a href="${aggL10n.premiumUrl}" target="_blank" class="button button-primary">
                            ${aggL10n.upgradeButton}
                        </a>
                    </div>
                </div>
            `;

            const modal = document.createElement('div');
            modal.innerHTML = modalHtml;
            document.body.appendChild(modal.firstElementChild);

            setTimeout(() => {
                modal.firstElementChild.remove();
            }, 3000);
        }

        // Button click handlers
        buttonGroups.forEach(group => {
            const buttons = group.querySelectorAll('.agg-button');
            const hiddenInput = group.nextElementSibling;
            
            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    if (button.classList.contains('agg-premium-feature')) {
                        showUpgradeModal();
                        return;
                    }

                    // Update active state
                    group.querySelectorAll('.agg-button').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    button.classList.add('active');
                    hiddenInput.value = button.dataset.value;
                });
            });
        });
    });
})(jQuery);