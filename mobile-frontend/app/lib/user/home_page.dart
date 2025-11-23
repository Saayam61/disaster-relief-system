import 'package:app/main_layout.dart';
import 'package:app/models/volunteer.dart';
import 'package:app/providers/user_provider.dart';
import 'package:app/providers/volapply_provider.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

class HomePage extends StatefulWidget {
  const HomePage({super.key});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {

  final _formKey = GlobalKey<FormState>();
  final _formKey2 = GlobalKey<FormState>();

  late TextEditingController _nameController;
  late TextEditingController _phoneController;
  late TextEditingController _emailController;
  late TextEditingController _addressController;
  late TextEditingController _skillController;
  late TextEditingController _availabilityController;
  String? _selectedStatus;
  
  @override
  void initState() {
    super.initState();

    _nameController = TextEditingController();
    _phoneController = TextEditingController();
    _emailController = TextEditingController();
    _addressController = TextEditingController();
    _skillController = TextEditingController();
    _availabilityController = TextEditingController();

    WidgetsBinding.instance.addPostFrameCallback((_) async {
      await Provider.of<UserProvider>(context, listen: false).fetchCurrentUser();
      final user = Provider.of<UserProvider>(context, listen: false).user;

      if (user != null) {
        _nameController.text = user.name;
        _phoneController.text = user.phone;
        _emailController.text = user.email;
        _addressController.text = user.address;
      }

      await Provider.of<VolapplyProvider>(context, listen: false).fetchCurrentVolunteer();
      final volunteer = Provider.of<VolapplyProvider>(context, listen: false).volunteer;
      if (volunteer != null) {
        _skillController.text = volunteer.skills ?? '';
        _availabilityController.text = volunteer.availability ?? '';
        _selectedStatus = volunteer.status;
      }
    });
  }

  @override
  void dispose() {
    _nameController.dispose();
    _phoneController.dispose();
    _emailController.dispose();
    _addressController.dispose();
    _skillController.dispose();
    _availabilityController.dispose();
    super.dispose();
  }

  void _submitV() async {
    if (_formKey2.currentState!.validate()) {
      try {
        await Provider.of<VolapplyProvider>(context, listen: false).updateVolunteer(
          skills: _skillController.text,
          availability: _availabilityController.text,
          status: _selectedStatus?.toLowerCase() ?? 'inactive',
        );
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Details updated successfully!')),
        );
      } catch (e) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Failed to update details')),
        );
      }
    }
  }

  void _submit() async {
    if (_formKey.currentState!.validate()) {
      try {
        await Provider.of<UserProvider>(context, listen: false).updateUser(
          name: _nameController.text,
          phone: _phoneController.text,
          email: _emailController.text,
          address: _addressController.text,
        );

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('User updated successfully!')),
        );
      } catch (e) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Failed to update user')),
        );
      }
    }
  }

  String? _validateNotEmpty(String? value, String fieldName) {
    if (value == null || value.trim().isEmpty) {
      return '$fieldName cannot be empty';
    }
    return null;
  }

  String? _validatePhone(String? value) {
    if (value == null || value.trim().isEmpty) {
      return 'Phone number cannot be empty';
    }
    final phoneRegex = RegExp(r'^\d{10}$');
    if (!phoneRegex.hasMatch(value.trim())) {
      return 'Enter a valid 10-digit phone number';
    }
    return null;
  }

  String? _validateEmail(String? value) {
    if (value == null || value.trim().isEmpty) return 'Email cannot be empty';
    final emailRegex = RegExp(r'^[^@]+@[^@]+\.[^@]+');
    if (!emailRegex.hasMatch(value)) return 'Enter a valid email';
    return null;
  }
  
  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<UserProvider>(context);
    final user = provider.user;
    final volprovider = Provider.of<VolapplyProvider>(context);
    final volunteer = volprovider.volunteer;
    // print(volunteer);
    return MainLayout(
      child: SingleChildScrollView(
        padding: EdgeInsets.only(bottom: 20),
        child: Column(
          children: [
            Card(
              margin: EdgeInsets.all(16),
              color: Colors.blueGrey,
              child: Padding(
                padding: const EdgeInsets.all(8.0),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.home, color: Colors.white),
                    SizedBox(width: 10),
                    Text(
                      'Home', 
                      style: TextStyle(
                        color: Colors.white
                      ),
                    )
                  ],
                ),
              ),
            ),
            Card(
              elevation: 3,
              margin: EdgeInsets.all(16),
              color: Colors.amber.shade50,
              child: Padding(
                padding: EdgeInsets.all(8),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        "Edit User Details",
                        style: Theme.of(context).textTheme.headlineSmall,
                      ),
                      SizedBox(height: 16),
            
                      // Name
                      TextFormField(
                        controller: _nameController,
                        decoration: InputDecoration(
                          labelText: "Name",
                          border: OutlineInputBorder(),
                        ),
                        validator: (val) => _validateNotEmpty(val, "Name"),
                      ),
                      SizedBox(height: 12),
            
                      // Phone
                      TextFormField(
                        controller: _phoneController,
                        decoration: InputDecoration(
                          labelText: "Phone",
                          border: OutlineInputBorder(),
                        ),
                        keyboardType: TextInputType.phone,
                        validator: _validatePhone,
                      ),
                      SizedBox(height: 12),
            
                      // Email
                      TextFormField(
                        controller: _emailController,
                        decoration: InputDecoration(
                          labelText: "Email",
                          border: OutlineInputBorder(),
                        ),
                        keyboardType: TextInputType.emailAddress,
                        validator: _validateEmail,
                      ),
                      SizedBox(height: 12),
            
                      // Address
                      TextFormField(
                        controller: _addressController,
                        decoration: InputDecoration(
                          labelText: "Address",
                          border: OutlineInputBorder(),
                        ),
                        validator: (val) => _validateNotEmpty(val, "Address"),
                      ),
                      SizedBox(height: 12),
            
                      TextFormField(
                        decoration: InputDecoration(
                          labelText: 'Latitude',
                          border: OutlineInputBorder(),
                        ),
                        controller: TextEditingController(
                          text: user?.latitude.toString() ?? '',
                        ),
                        readOnly: true,
                      ),
                      SizedBox(height: 12),
            
                      TextFormField(
                        decoration: InputDecoration(
                          labelText: 'Longitude',
                          border: OutlineInputBorder(),
                        ),
                        controller: TextEditingController(
                          text: user?.longitude.toString() ?? '',
                        ),
                        readOnly: true,
                      ),
                      SizedBox(height: 20),
            
                      Align(
                        alignment: Alignment.center,
                        child: ElevatedButton.icon(
                          label: Text("Update User"),
                          icon: Icon(Icons.save, color: Colors.white,),
                          onPressed: _submit,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.deepPurple, // Button background
                            foregroundColor: Colors.white,      // Text color
                            padding: EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                            textStyle: TextStyle(
                              fontSize: 16,
                            ),
                          ),
                        ),
                      ),
                      SizedBox(height: 10),
                    ],
                  ),
                ),
              ),
            ),
            if(user?.role == 'Volunteer')
            Card(
              elevation: 3,
              margin: EdgeInsets.all(16),
              color: Colors.amber.shade50,
              child: Padding(
                padding: EdgeInsets.all(8),
                child: Form(
                  key: _formKey2,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        "Edit Volunteer Details",
                        style: Theme.of(context).textTheme.headlineSmall,
                      ),
                      SizedBox(height: 16),
            
                      // Relief Center
                      TextFormField(
                        controller: TextEditingController(
                          text: volunteer?.centerName?.toLowerCase() ?? '',
                        ),
                        decoration: InputDecoration(
                          labelText: "Affliated Relief Center",
                          border: OutlineInputBorder(),
                        ),
                        readOnly: true,
                      ),
                      SizedBox(height: 12),
            
                      // Organization
                      TextFormField(
                        controller: TextEditingController(
                          text: volunteer?.orgName?.toLowerCase() ?? '',
                        ),
                        decoration: InputDecoration(
                          labelText: "Affliated Organization",
                          border: OutlineInputBorder(),
                        ),
                        readOnly: true,
                      ),
                      SizedBox(height: 12),
            
                      // Approval Status
                      TextFormField(
                        controller: TextEditingController(
                          text: volunteer?.approvalStatus.toLowerCase(),
                        ),
                        decoration: InputDecoration(
                          labelText: "Approval Status",
                          border: OutlineInputBorder(),
                        ),
                        readOnly: true,
                      ),
                      SizedBox(height: 12),
            
                      // Skills
                      TextFormField(
                        controller: _skillController,
                        decoration: InputDecoration(
                          labelText: "Skills",
                          border: OutlineInputBorder(),
                        ),
                        validator: (val) => _validateNotEmpty(val, "Skills"),
                        maxLines: 4,
                      ),
                      SizedBox(height: 12),
        
                      // Availability
                      TextFormField(
                        controller: _availabilityController,
                        decoration: InputDecoration(
                          labelText: "Availability",
                          border: OutlineInputBorder(),
                        ),
                        validator: (val) => _validateNotEmpty(val, "Availability"),
                      ),
                      SizedBox(height: 12),
        
                      DropdownButtonFormField<String>(
                          value: _selectedStatus,
                          items: statusMap.entries
                              .map((entry) => DropdownMenuItem(
                                    value: entry.value,
                                    child: Text(entry.key),
                                  ))
                              .toList(),
                          onChanged: (val) => setState(() => _selectedStatus = val),
                          decoration: const InputDecoration(labelText: 'Status'),
                          validator: (val) => val == null ? 'Select status' : null,
                        ),
                      SizedBox(height: 20),
            
                      Align(
                        alignment: Alignment.center,
                        child: ElevatedButton.icon(
                          label: Text("Update Details"),
                          icon: Icon(Icons.save, color: Colors.white,),
                          onPressed: _submitV,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.deepPurple, // Button background
                            foregroundColor: Colors.white,      // Text color
                            padding: EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                            textStyle: TextStyle(
                              fontSize: 16,
                            ),
                          ),
                        ),
                      ),
                      SizedBox(height: 10),
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