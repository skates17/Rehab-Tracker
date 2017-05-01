// EMPI Usage Monitoring
// Blake Hewgill & Javier Bunuel, University of Vermont
// Capstone Design, Spring 2017
boolean testMode = true;

#include <EEPROMex.h>
#include <SPI.h>
#include <lib_aci.h>
#include <aci_setup.h>
#include <RBL_nRF8001.h>
#include <RBL_services.h>

int buttonPin = 8; // pin for sync button
int LED1 = 2; // Status lights
int LED2 = 3;
int LED3 = 4;
int sensorPin1 = A0;    // select the input pin for channel 1
int sensorPin2 = A1;    // select the input pin for channel 2
int sensorValue = 0;  // variable to store the value coming from the sensor
int sampleNum1 = 0;  // Sample number
int sampleNum2 = 0;  // Sample number
int sampleArray1[140]; // Should be 90 samples in an hour long session (one each 40 seconds). Extras to be safe.
long arrayTot1 = 0; // A running sum of the contents of sampleArray to be divided by sampleNum for averaging
float arrayAvg1 = 0; // A running avg of the above
int sampleArray2[140]; // Should be 90 samples in an hour long session (one each 40 seconds). Extras to be safe.
long arrayTot2 = 0; // A running sum of the contents of sampleArray to be divided by sampleNum for averaging
float arrayAvg2 = 0; // A running avg of the above
int sessionCount; //= EEPROM.write(0, 0x00);
int currentAddress; //= EEPROM.write(1,0x15);
char comma = ',';
char newline = '\n';
float sessionComp;
int ant1=0;
int next1=0;
int maxVal1=0;
int ant2=0;
int next2=0;
int maxVal2=0;


void setup(){
  // Initialize what we need in here
  EEPROM.setMaxAllowedWrites(32768);
  EEPROM.writeInt(0,1);
  EEPROM.writeInt(2,2);
  sessionCount = EEPROM.readInt(0);
  currentAddress = EEPROM.readInt(2);
  // UPLOAD EEPROM SHITE
  pinMode(LED1, OUTPUT);
  pinMode(LED2, OUTPUT);
  pinMode(LED3, OUTPUT);
  if (testMode){ //start serial com
    Serial.begin(57600);
    Serial.print("SessionCount = ");
    Serial.println(sessionCount);
  }
  ble_begin();
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
void loop()
{
  Serial.println("yo");
  while(1)
  {
    ButtonInterrupt();
  }
  
  if (testMode){
    Serial.println("TEST MODE ACTIVE");
  }
  Serial.print("Pin1: ");
  Serial.print(analogRead(sensorPin1));
  Serial.print(" ; Pin2: ");
  Serial.println(analogRead(sensorPin2));

  ButtonInterrupt();

  // Comparison Loop
  // Channel 1
  ant1=next1;
  next1=analogRead(sensorPin1);
  delay(1000);
  if (ant1>next1){
    if (maxVal1>ant1){
      maxVal1=ant1;
      //SEND
      Serial.print("MaxDigiVoltage = ");
      Serial.println(maxVal1);
      //Intensity Map
      ArrayAdd(IntensityMap(maxVal1),1);
      maxVal1=0;
      ButtonInterrupt();
      delay(500);
      if (((sampleNum1+1) % 10) == 0){
        WriteStorage();
        ButtonInterrupt();
      }
    }
  } else {
    maxVal1=next1;
  }

  ButtonInterrupt();

  // Channel 2
  ant2=next2;
  next2=analogRead(sensorPin2);
  if (ant2>next2){
    if (maxVal2>ant2){
      maxVal2=ant2;
      //SEND
      Serial.print("MaxDigiVoltage = ");
      Serial.println(maxVal2);
      //Intensity Map
      ArrayAdd(IntensityMap(maxVal2),2);
      maxVal2=0;
      delay(500);
      if (((sampleNum2+1) % 10) == 0){
        WriteStorage();
      }
    }
  }else{
    maxVal2=next2;
  }

  ButtonInterrupt();

    Serial.print("currentAddress = HEX ");
    Serial.println(currentAddress);

    Serial.print("SampleNumber 1 = "); 
    Serial.println(sampleNum1);

    Serial.print("SampleNumber 2 = "); 
    Serial.println(sampleNum2);
    Serial.println("------------Loop Complete-------------");
  
}

////////////Button Inter//////////////////////////////////////////////////////////
void ButtonInterrupt(){
  
  int address=0;
  unsigned char bytes[255];
  unsigned char value;
  
  if (digitalRead(buttonPin)==1 || sampleNum1>90 || sampleNum2>90){

    ble_set_pins(6,7);
    ble_set_name("SEED 15");
    while (!ble_connected()){
      Serial.println("BLE Not Connected");
      ble_do_events();
    }
    
    if ( ble_connected() ){
      Serial.println("BLE Connected");
      while(true) {
        value = EEPROM.readByte(address);
        // Instead of EEPROM.length() we can put the number of bytes of the file if we know the extension
        // avoiding sending many 0 in a row
        while (address < 255) {
          bytes[address]=value;
          Serial.write(value);
          address = address + 1;
          value = EEPROM.readByte(address);
        }
        ble_write_bytes(bytes,255);
        address = 0;
        delay(1000); // We wait for an answer if its true, he has receive it so we go out of the loop, if not, we send it again
        if (ble_read()==true) { // We may have to change true for the byte that corresponds to true
          ble_disconnect();
        }
      }
    }
  //}
  ble_do_events();
}

///////////////////////Writing to EEPROM///////////////////////////////////////////////////
void WriteStorage(){
  EEPROM.updateInt(currentAddress,sessionCount);
  currentAddress = currentAddress + 2; //increase by int
  EEPROM.updateByte(currentAddress,comma);
  currentAddress++; //increase by char
  EEPROM.updateFloat(currentAddress,arrayAvg1);
  currentAddress = currentAddress + 4; //increase by float
  EEPROM.updateByte(currentAddress,comma);
  currentAddress++; //increase by char
  EEPROM.updateFloat(currentAddress,arrayAvg2);
  currentAddress = currentAddress + 4; //increase by float
  EEPROM.updateByte(currentAddress,comma);
  currentAddress++; //increase by char
  EEPROM.updateFloat(currentAddress,sessionComp);
  currentAddress = currentAddress + 4;  //increase by float
  EEPROM.updateByte(currentAddress,newline);
  currentAddress++; //increase by char
  if (sampleNum1>sampleNum2){
    if (sampleNum1<90){ //Not done - these addresses will be rewritten this session
    currentAddress = currentAddress - 18;
    }else{ //session is done
    sessionCount++;
    EEPROM.updateInt(0,sessionCount);
    }
    if (testMode){
      Serial.print(sessionCount);
      Serial.print(',');
      Serial.print(arrayAvg1);
      Serial.print(',');
      Serial.print(arrayAvg2);
      Serial.print(',');
      Serial.print(sessionComp);
      Serial.println(';');
    }
  }else{
    if (sampleNum2<90){ //Not done - these addresses will be rewritten this session
    currentAddress = currentAddress - 18;
    }else{ //session is done
    sessionCount++;
    EEPROM.updateInt(0,sessionCount);
    }
    if (testMode){
      Serial.print(sessionCount);
      Serial.print(',');
      Serial.print(arrayAvg1);
      Serial.print(',');
      Serial.print(arrayAvg2);
      Serial.print(',');
      Serial.print(sessionComp);
      Serial.println(';');
    }
  }
}
//////////////////////////////////////////////////////////////////////////////////////////
void ArrayAdd(float intensityValue, int channel) {
  if (intensityValue>0){
    if (channel==1){
      sampleArray1[sampleNum1] = intensityValue;
      sampleNum1++;
      arrayTot1 = arrayTot1 + intensityValue;
      arrayAvg1 = arrayTot1/(sampleNum1+1);
      sessionComp = float(sampleNum1)/90;
      if (testMode){
        Serial.print("ArrayAvg = ");
        Serial.print(arrayAvg1);
        Serial.print(" and SessionComplete = ");
        Serial.print(sessionComp*100);
        Serial.println("%");
      }
    }else{
      sampleArray2[sampleNum2] = intensityValue;
      sampleNum2++;
      arrayTot2 = arrayTot2 + intensityValue;
      arrayAvg2 = arrayTot2/(sampleNum2+1);
      sessionComp = float(sampleNum2)/90;
      if (testMode){
        Serial.print("ArrayAvg = ");
        Serial.print(arrayAvg2);
        Serial.print(" and SessionComplete = ");
        Serial.print(sessionComp*100);
        Serial.println("%");
      }
    }
  }
}
///////////////////////////////////////////////////////////////////////////////////////////
float IntensityMap(int sensorValue){
  float intensity;
  // Nested if’s/elseif reduce processor load and speed up the cycle time
  if (sensorValue < 683)
  {
    if (sensorValue < 393) 
    {
      if (sensorValue < 203)
      {
        if (sensorValue < 101)
        {
          if (sensorValue < 50)
          {
            intensity = .0;
          }
          else if (sensorValue >= 50)
          {
            intensity = 1.0;
          }
        }
        else if (sensorValue >= 101)
        {
          if (sensorValue < 151)
          {
            intensity = 1.5;
          }
          else if (sensorValue >= 151)
          {
            intensity = 2.0;
          }
        }
      }
      else if (sensorValue >= 203)
      {
        if (sensorValue < 299)
        {
          if (sensorValue < 251)
          {
            intensity = 2.5;
          }
          else if (sensorValue >= 251)
          {
            intensity = 3.0;
          }
        }
        else if (sensorValue >= 299)
        {
          if (sensorValue < 345)
          {
            intensity = 3.5;
          }
          else if (sensorValue >= 345)
          {
            intensity = 4.0;
          }
        }
      }
    }
    else if (sensorValue >= 393)
    {
      if (sensorValue < 594)
      {
        if (sensorValue < 479)
        {
          if (sensorValue < 438)
          {
            intensity = 4.5;
          }
          else if (sensorValue >= 438)
          {
            intensity = 5.0;
          }
        }
        else if (sensorValue >= 479)
        {
          if (sensorValue < 538)
          {
            intensity = 5.5;
          }
          else if (sensorValue >= 538)
          {
            intensity = 6.0;
          }

        }
      }
      else if (sensorValue >= 594)
      {
        if (sensorValue < 658)
        {
          if (sensorValue < 627)
          {
            intensity = 6.5;
          }
          else if (sensorValue >= 627)
          {
            intensity = 7.0;
          }
        }
        else if (sensorValue >= 658)
        {
          intensity = 7.5;
        }
      }
    }
  }
  else if (sensorValue >= 683)
  {
    if (sensorValue < 876)
    {
      if (sensorValue < 786)
      {
        if (sensorValue < 737)
        {
          if (sensorValue < 706)
          {
            intensity = 8.0;
          }
          else if (sensorValue >= 706)
          {
            if (sensorValue < 724)
            {
              intensity = 8.5;
            }
            else if (sensorValue >= 724)
            {
              intensity = 9.0;
            }
          }
        }
        else if (sensorValue >= 737)
        {
          if (sensorValue < 758)
          {
            intensity = 9.5;
          }
          else if (sensorValue >= 758)
          {
            intensity = 10.0;
          }
        }
      }
      else if (sensorValue >= 786)
      {
        if (sensorValue < 816)
        {
          intensity = 11.0;
        }
        else if (sensorValue >= 816)
        {
          if (sensorValue < 847)
          {
            intensity = 12.0;
          }
          else if (sensorValue >= 847)
          {
            intensity = 13.0;
          }
        }
      }
    }
    else if (sensorValue >= 876)
    {
      if (sensorValue < 961)
      {
        if (sensorValue < 933)
        {
          if (sensorValue < 905)
          {
            intensity = 14.0;
          }
          else if (sensorValue >= 905)
          {
            intensity = 15.0;
          }
        }
        else if (sensorValue >= 933)
        {
          intensity = 16.0;
        }
      }
      else if (sensorValue >= 961)
      {
        if (sensorValue < 1010)
        {
          if (sensorValue < 989)
          {
            intensity = 17.0;
          }
          else if (sensorValue >= 989)
          {
            intensity = 18.0;
          }
        }
        else if (sensorValue >= 1010)
        {
          if (sensorValue < 1020)
          {
            intensity = 19.0;
          }
          else if (sensorValue >= 1020)
          {
            intensity = 20.0;
          }
        }
      }    

    }
  }
  intensity = (intensity*2);
  if (testMode){
    Serial.print("Intensity = ");
    Serial.println(intensity);
  }
  return intensity;
}
