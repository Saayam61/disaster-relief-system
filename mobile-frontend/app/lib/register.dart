import 'package:app/login.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:geolocator/geolocator.dart';
import 'package:shared_preferences/shared_preferences.dart';

class RegisterPage extends StatefulWidget {
  @override
  _RegisterPageState createState() => _RegisterPageState();
}

class _RegisterPageState extends State<RegisterPage> {
  final _formKey = GlobalKey<FormState>();

  TextEditingController nameController = TextEditingController();
  TextEditingController phoneController = TextEditingController();
  TextEditingController emailController = TextEditingController();
  TextEditingController passwordController = TextEditingController();
  TextEditingController confirmPasswordController = TextEditingController();
  TextEditingController latitudeController = TextEditingController();
  TextEditingController longitudeController = TextEditingController();

  bool loading = false;

  @override
  void initState() {
    super.initState();
    _getCurrentLocation();
  }

  Future<void> _getCurrentLocation() async {
    LocationPermission permission = await Geolocator.requestPermission();
    if (permission == LocationPermission.denied || permission == LocationPermission.deniedForever) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text("GPS permission denied")));
      return;
    }

    Position position = await Geolocator.getCurrentPosition(desiredAccuracy: LocationAccuracy.high);
    setState(() {
      latitudeController.text = position.latitude.toStringAsFixed(7);
      longitudeController.text = position.longitude.toStringAsFixed(7);
    });
  }

  Future<void> _register() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => loading = true);

    final Map<String, dynamic> data = {
      "name": nameController.text,
      "phone": phoneController.text,
      "email": emailController.text,
      "password": passwordController.text,
      "password_confirmation": confirmPasswordController.text,
      "role": "General User",
      "latitude": latitudeController.text,
      "longitude": longitudeController.text,
    };

    final response = await http.post(
      Uri.parse("http://localhost:8000/api/register"),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: json.encode(data),
    );

    setState(() => loading = false);

    if (response.statusCode == 201 || response.statusCode == 200) {
      // Parse token from the response body (ensure this matches your backend response)
      var responseBody = json.decode(response.body);
      var token = responseBody['access_token'];  // Make sure this matches your backend response

      // Save token to SharedPreferences
      SharedPreferences prefs = await SharedPreferences.getInstance();
      await prefs.setString('auth_token', token);

      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text("Registration successful")));
      Navigator.pushReplacement(
        context, 
        MaterialPageRoute(builder: (context) => LoginPage())
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text("Registration failed: ${response.body}")));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          "User Registration", 
          style: TextStyle(fontSize: 32, color: Colors.deepPurple, fontWeight: FontWeight.bold)
        ), 
        centerTitle: true
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: loading
            ? Center(child: CircularProgressIndicator())
            : Form(
                key: _formKey,
                child: ListView(
                  children: [
                    _buildTextField(nameController, "Name", TextInputType.name),
                    _buildTextField(phoneController, "Phone", TextInputType.phone),
                    _buildTextField(emailController, "Email", TextInputType.emailAddress),
                    _buildTextField(passwordController, "Password", TextInputType.visiblePassword, obscureText: true),
                    _buildTextField(confirmPasswordController, "Confirm Password", TextInputType.visiblePassword, obscureText: true),
                    _buildTextField(latitudeController, "Latitude", TextInputType.number, readOnly: true),
                    _buildTextField(longitudeController, "Longitude", TextInputType.number, readOnly: true),
                    SizedBox(height: 20),
                    ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.deepPurple, 
                        padding: EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      onPressed: _register,
                      child: Text("Register", style: TextStyle(fontSize: 16, color: Colors.white)),
                    ),
                    SizedBox(height: 10),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text("Already have an account? "),
                        GestureDetector(
                          onTap: () {
                            Navigator.pushReplacementNamed(context, '/login'); // Navigate to login page
                          },
                          child: Text(
                            "Login here",
                            style: TextStyle(color: Colors.deepPurple, fontWeight: FontWeight.bold),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
      ),
    );
  }

  Widget _buildTextField(TextEditingController controller, String label, TextInputType type,
      {bool obscureText = false, bool readOnly = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: TextFormField(
        controller: controller,
        keyboardType: type,
        obscureText: obscureText,
        readOnly: readOnly,
        decoration: InputDecoration(labelText: label, border: OutlineInputBorder()),
        validator: (value) => value == null || value.isEmpty ? "$label is required" : null,
      ),
    );
  }
}
