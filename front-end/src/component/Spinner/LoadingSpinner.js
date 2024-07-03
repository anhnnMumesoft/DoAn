import React from 'react';
import { Spinner } from 'react-bootstrap';
import './LoadingSpinner.scss';

const LoadingSpinner = () => {
    console.log('LoadingSpinner is rendered');
    return (
        <div className="loading-overlay">
            <Spinner animation="border" variant="primary" />
        </div>
    );
};
export default LoadingSpinner;