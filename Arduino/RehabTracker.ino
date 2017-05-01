/* 
 * Testing Script by Sean Kates
 * Hard coding data to write to the Central through ble_write_array
 * Using this script to bypass the logic of the SEED teams code
 */
 
// libraries and packages we need
#include <SPI.h>
#include <boards.h>
#include <RBL_nRF8001.h>

// create an unsigned char array of length 255 (what the SEED team has)
unsigned char bytes[255];

void setup()
{ 
  // Init. and start BLE library.
  ble_begin();
}

void loop()
{
  while ( !ble_connected() )
  {
    // This sends out a ping advertising the peripheral
    ble_do_events();
  }
  
  if ( ble_connected() )
  {

    // If the BLE is connected, add values to the bytes[] array
    // Can only write a character at a time
    // Adjust the pointer each time
    
    // set the pointer for the array to 0
    unsigned char address = 0;
    
    bytes[address] = '1';
    address++;
    
    bytes[address] = ',';
    address++;
    
    bytes[address] = '0';
    address++;
    
    bytes[address] = ',';
    address++;
    
    bytes[address] = '9';
    address++;
    
    bytes[address] = ',';
    address++;
    
    bytes[address] = '.';
    address++;
    
    bytes[address] = '9';
    address++;

    bytes[address] = '7';
    address++;
    
    // Need to write a new line for the .csv config
    bytes[address] = '\n';
    address++;
    
    bytes[address] = '2';
    address++;
    
    bytes[address] = ',';
    address++;
    
    bytes[address] = '0';
    address++;
    
    bytes[address] = ',';
    address++;
    
    bytes[address] = '1';
    address++;

    bytes[address] = '5';
    address++;
    
    bytes[address] = ',';
    address++;
    
    bytes[address] = '.';
    address++;
    
    bytes[address] = '9';
    address++;

    bytes[address] = '6';
    address++;
    
    // Write the unsigned char array to phone
    // The ble_write_bytes function just iterates through the array
    // And does a ble_write for each character
    // address is the length of the array so that it can iterate
    ble_write_bytes(bytes,address);
  }

  // This actually sends the data over the connection
  ble_do_events();

  // There seems to be some latency, so delaying helps write what we want
  delay(1500);
}

