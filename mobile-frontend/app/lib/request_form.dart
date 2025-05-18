import 'package:app/main_layout.dart';
import 'package:app/models/request.dart';
import 'package:app/providers/request_provider.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

class RequestForm extends StatefulWidget {
  final Request? existingRequest;

  const RequestForm({super.key, this.existingRequest});

  @override
  State<RequestForm> createState() => _RequestFormState();
}

class _RequestFormState extends State<RequestForm> {
  final _formKey = GlobalKey<FormState>();

  final _descriptionController = TextEditingController();
  final _statusController = TextEditingController();
  final _quantityController = TextEditingController();
  final _unitController = TextEditingController();
  final _centerController = TextEditingController();

  String? _selectedCenter;
  String? _selectedType;
  String? _selectedUrgency;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<RequestProvider>(context, listen: false).getUserRequests();
    });
    if (widget.existingRequest != null) {
      final req = widget.existingRequest!;
      _descriptionController.text = req.description;
      _quantityController.text = req.quantity.toString();
      _statusController.text = req.status;
      _unitController.text = req.unit ?? '';
      _centerController.text = req.centerName;
      _selectedType = req.type;
      _selectedUrgency = req.urgency;   
    }
    else {
    _statusController.text = 'pending';
    }
  }

  void _submitForm() {
    if (_formKey.currentState!.validate()) {
      final newRequest = Request(
        id: widget.existingRequest?.id ?? '',
        centerName: _selectedCenter ?? '',
        type: _selectedType?.toLowerCase() ?? '',
        status: widget.existingRequest?.status ?? 'pending',
        description: _descriptionController.text.trim(),
        quantity: _quantityController.text.trim().isNotEmpty
            ? int.parse(_quantityController.text.trim())
            : null,
        unit: _unitController.text.trim().isNotEmpty
            ? _unitController.text.trim()
            : null,
        urgency: _selectedUrgency?.toLowerCase() ?? '',
        createdAt: DateTime.now(),
      );

      final provider = Provider.of<RequestProvider>(context, listen: false);

      if (widget.existingRequest == null) {
        provider.addRequest(newRequest);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Request added successfully!')),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Request updated successfully!')),
        );
        provider.updateRequest(newRequest); 
      }

      Navigator.pop(context);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Operation failed!')),
        );
    }
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<RequestProvider>(context);
    final centerData = provider.centerData;
    return MainLayout(
      child: SingleChildScrollView(
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.only(top: 8, left: 8),
              child: Row(
                children: [
                  IconButton(
                    icon: Icon(Icons.arrow_back),
                    onPressed: () {
                      Navigator.pop(context);
                    },
                  ),
                  Text(
                    widget.existingRequest != null ? "Edit Request" : "New Request",
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ),
            ),
            // 📝 Form logic
            Card(
              elevation: 3,
              margin: EdgeInsets.fromLTRB(16, 8, 16, 16),
              color: Colors.amber.shade50,
              child: Padding(
                padding: const EdgeInsets.all(8),
                child: Form(
                  key: _formKey,
                  child: Column(
                    children: [
                      widget.existingRequest == null ?
                      DropdownButtonFormField<String>(
                        value: _selectedCenter,
                        items: centerData.map((c) => DropdownMenuItem(value: c.id, child: Text(c.centerName))).toList(),
                        onChanged: (val) => setState(() => _selectedCenter = val),
                        decoration: const InputDecoration(labelText: 'Relief Center'),
                        validator: (val) => val == null ? 'Select a relief center' : null,
                      ) :
                      TextFormField(
                        controller: _centerController,
                        decoration: const InputDecoration(
                          labelText: 'Relief Center',
                          border: OutlineInputBorder(),
                        ),
                        readOnly: true,
                      ),
                      
                      const SizedBox(height: 12),
                
                      DropdownButtonFormField<String>(
                        value: _selectedType,
                        items: typeMap.entries
                            .map((entry) => DropdownMenuItem(
                                  value: entry.value, 
                                  child: Text(entry.key), 
                                ))
                            .toList(),
                        onChanged: (val) => setState(() => _selectedType = val),
                        decoration: const InputDecoration(labelText: 'Request Type'),
                        validator: (val) => val == null ? 'Select a type' : null,
                      ),
                      const SizedBox(height: 12),
                
                      DropdownButtonFormField<String>(
                        value: _selectedUrgency,
                        items: urgencyMap.entries
                            .map((entry) => DropdownMenuItem(
                                  value: entry.value,
                                  child: Text(entry.key),
                                ))
                            .toList(),
                        onChanged: (val) => setState(() => _selectedUrgency = val),
                        decoration: const InputDecoration(labelText: 'Urgency'),
                        validator: (val) => val == null ? 'Select urgency' : null,
                      ),
                
                      const SizedBox(height: 12),
                
                      TextFormField(
                        controller: _descriptionController,
                        decoration: const InputDecoration(
                          labelText: 'Description',
                          border: OutlineInputBorder(),
                        ),
                        maxLines: 4,
                        validator: (val) => val == null || val.isEmpty ? 'Enter description' : null,
                      ),
                      const SizedBox(height: 12),
                      TextFormField(
                        controller: _quantityController,
                        keyboardType: TextInputType.number,
                        decoration: const InputDecoration(
                          labelText: 'Quantity',
                          border: OutlineInputBorder(),
                        ),
                        validator: (val) {
                          if (val == null || val.trim().isEmpty) return null; 
                          if (int.tryParse(val.trim()) == null) return 'Invalid number';
                          return null;
                        },
                      ),
                      const SizedBox(height: 20),
                      TextFormField(
                        controller: _unitController,
                        decoration: const InputDecoration(
                          labelText: 'Unit',
                          border: OutlineInputBorder(),
                        ),
                        validator: (val) { 
                          if (val == null || val.trim().isEmpty) return null;
                          if (!RegExp(r'^[a-zA-Z]+$').hasMatch(val.trim())) return 'Invalid unit';
                          return null;                        
                        },
                      ),
                      const SizedBox(height: 12),
                
                      TextFormField(
                        controller: _statusController,
                        decoration: const InputDecoration(
                          labelText: 'Status',
                          border: OutlineInputBorder(),
                        ),
                        readOnly: true,
                      ),
                      // const SizedBox(height: 12),                
                      SizedBox(height: 15),
                          
                      Align(
                        alignment: Alignment.center,
                        child: ElevatedButton.icon(
                          icon: const Icon(Icons.save, color: Colors.white,),
                          label: Text(widget.existingRequest != null ? 'Update' : 'Submit'),
                          onPressed: _submitForm,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.deepPurple, 
                            foregroundColor: Colors.white,
                            padding: EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                            textStyle: TextStyle(
                              fontSize: 16,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}