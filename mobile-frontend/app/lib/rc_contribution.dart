import 'package:app/main_layout.dart';
import 'package:app/providers/contribution_provider.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

class RcContribution extends StatefulWidget {
  final int userId;
  const RcContribution({super.key, required this.userId});

  @override
  State<RcContribution> createState() => _RcContributionState();
}

class _RcContributionState extends State<RcContribution> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final provider = Provider.of<ContributionProvider>(context, listen: false);
      // await provider.getVolContributions(userId: widget.userId);
      // con = provider.contribution;  
    });
  }
  @override
  Widget build(BuildContext context) {
    return MainLayout(
      child: Text("data")
    );
  }
}