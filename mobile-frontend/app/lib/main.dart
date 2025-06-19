import 'package:app/providers/contribution_provider.dart';
import 'package:app/providers/newsfeed_provider.dart';
import 'package:app/providers/request_provider.dart';
import 'package:app/providers/search_provider.dart';
import 'package:app/user/home_page.dart';
import 'package:app/login.dart';
import 'package:app/providers/notification_provider.dart';
import 'package:app/register.dart';
import 'package:app/search.dart';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:provider/provider.dart';
import 'providers/user_provider.dart';

void main() {
  // FlutterError.onError = (FlutterErrorDetails details) {
  //   FlutterError.presentError(details);
  //   // print it to debug console
  //   print('error is this: ' +details.exceptionAsString());
  //       print('🕵️‍♂️ Stack trace: ${details.stack}');
  // };

  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => NotificationProvider()),
        ChangeNotifierProvider(create: (_) => UserProvider()),
        ChangeNotifierProvider(create: (_) => NewsFeedProvider()),
        ChangeNotifierProvider(create: (_) => ContributionProvider()),
        ChangeNotifierProvider(create: (_) => RequestProvider()),
        ChangeNotifierProvider(create: (_) => SearchProvider()),
      ],
      child: MaterialApp(
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
          '/search': (context) => SearchPage(),
        },
      ),
    );
  }
  
  Future<bool> _checkIfLoggedIn() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    String? token = prefs.getString('login_token');  // Retrieve token
    return token != null && token.isNotEmpty;
  }
}

