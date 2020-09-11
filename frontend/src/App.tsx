import { Box } from '@material-ui/core';
import React from 'react';
// import logo from './logo.svg';
import './App.css';
import { Navbar } from './components/Navbar';
import { Page } from './components/Page';

function App() {
  return (
    <React.Fragment>
      <Navbar />
      <Box paddingTop={'70px'}>
        <Page title={'Categorias'}>
          Conteudo
        </Page>
      </Box>
    </React.Fragment>
  );
}

export default App;
