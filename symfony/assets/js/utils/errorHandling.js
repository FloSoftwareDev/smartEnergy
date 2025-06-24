export const showError = (message) => {
    // You can replace this with a more sophisticated notification system
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger alert-dismissible fade show';
    errorDiv.role = 'alert';
    errorDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.insertBefore(errorDiv, document.body.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        errorDiv.remove();
    }, 5000);
};

export const handleApiError = async (error) => {
    console.error('API Error:', error);
    
    if (error.status === 401) {
        // Unauthorized - redirect to login
        window.location.href = '/login';
        return;
    }
    
    if (error.status === 403) {
        showError('U heeft geen toegang tot deze functionaliteit.');
        return;
    }
    
    showError(error.message || 'Er is een onverwachte fout opgetreden.');
};

export const wrapApiCall = async (apiCall) => {
    try {
        return await apiCall();
    } catch (error) {
        await handleApiError(error);
        throw error;
    }
}; 