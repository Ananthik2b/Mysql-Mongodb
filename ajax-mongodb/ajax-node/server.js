const express = require('express');
const mongoose = require('mongoose');
const bodyParser = require('body-parser');

const app = express();
app.use(bodyParser.json());

mongoose.connect('mongodb+srv://k2bitteammail:9d7CBZCh20OquGoO@k2b-itteam-test-db.buah6.mongodb.net/record', {
    useNewUrlParser: true,
    useUnifiedTopology: true
}).then(() => console.log('MongoDB connected to database: record'))
  .catch(err => console.error('MongoDB connection failed:', err));

// MongoDB Schema
const RecordSchema = new mongoose.Schema({
    id: Number,
    name: String,
    email: String,
    created_at: { type: Date, default: Date.now }
});

const Record = mongoose.model('Record', RecordSchema);

// API endpoint to handle syncing with MongoDB
app.post('/sync-mongodb', async (req, res) => {
    const data = req.body;
    const operation = data.operation;

    try {
        if (operation === 'create' || operation === 'update') {
            // Create or update the record
            await Record.updateOne(
                { id: data.id },
                { $set: data },
                { upsert: true }
            );
            res.status(200).send({ message: 'Record synchronized successfully!' });
        } else if (operation === 'delete') {
            // Delete record from MongoDB
            await Record.deleteOne({ id: data.id });

            // Reassign IDs in MongoDB to reflect the new sequence
            const records = await Record.find().sort({ id: 1 });
            let newId = 1;
            for (const record of records) {
                await Record.updateOne({ _id: record._id }, { $set: { id: newId } });
                newId++;
            }

            res.status(200).send({ message: 'Record deleted and IDs synchronized in MongoDB!' });
        } else {
            res.status(400).send({ message: 'Invalid operation type.' });
        }
    } catch (error) {
        console.error(error);
        res.status(500).send({ error: 'Synchronization failed' });
    }
});

const PORT = 3000;
app.listen(PORT, () => console.log(`Node.js API running on port ${PORT}`));
