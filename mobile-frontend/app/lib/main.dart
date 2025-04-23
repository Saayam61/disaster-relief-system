import 'package:app/homepage.dart';
import 'package:app/login.dart';
import 'package:app/register.dart';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'DRS App',
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: Colors.deepPurple,
          brightness: Brightness.light, 
        ),
      ),
      home: FutureBuilder<bool>(
        future: _checkIfLoggedIn(),  // Check login state
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: CircularProgressIndicator()); // Show loading while checking
          }
          if (snapshot.hasData && snapshot.data == true) {
            return HomePage();  // If logged in, go to HomePage
          }
          return LoginPage();  // Otherwise, show register page
        },
      ),
      routes: {
        '/login': (context) => LoginPage(),
        '/register': (context) => RegisterPage(),
        '/home': (context) => HomePage(),
      },
    );
  }
  Future<bool> _checkIfLoggedIn() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    String? token = prefs.getString('auth_token');  // Retrieve token
    return token != null && token.isNotEmpty;
  }
}

