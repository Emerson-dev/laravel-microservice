import { Box } from '@material-ui/core';
import React from 'react';
import { BrowserRouter } from 'react-router-dom';
import Breadcrumbs from './components/breadcrumbs';
import { Navbar } from './components/Navbar';
import AppRouter from './routes/AppRouter';
import './App.css';

function App() {
  return (
    <React.Fragment>
      <BrowserRouter>
        <Navbar />
        <Box paddingTop={'70px'}>
          <Breadcrumbs />
          <AppRouter />
        </Box>
      </BrowserRouter>
    </React.Fragment>
  );
}

export default App;
