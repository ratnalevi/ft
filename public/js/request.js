const iothub = require('azure-iot-device');

const connectionString = "YOUR_IOT_HUB_CONNECTION_STRING";
const deviceId = "YOUR_DEVICE_ID";

const registry = iothub.Registry.fromConnectionString(connectionString);

const device = {
    deviceId: deviceId
};

registry.create(device, (err, deviceInfo, res) => {
    if (err) {
        console.error('Error creating device: ' + err.toString());
    } else {
        console.log('Device created successfully');
    }
});
